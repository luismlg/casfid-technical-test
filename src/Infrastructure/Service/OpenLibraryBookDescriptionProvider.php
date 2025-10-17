<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Book\Service\BookDescriptionProviderInterface;
use App\Domain\Book\ValueObject\BookDescription;
use App\Domain\Book\ValueObject\BookIsbn;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

/**
 * OpenLibraryBookDescriptionProvider
 * 
 * Servicio que consume la API pública de OpenLibrary para enriquecer
 * la información de libros mediante su ISBN.
 * 
 * @see https://openlibrary.org/dev/docs/api/books
 */
final class OpenLibraryBookDescriptionProvider implements BookDescriptionProviderInterface
{
    private const API_ENDPOINT = 'https://openlibrary.org/api/books';
    private const API_FORMAT = 'json';
    private const API_COMMAND = 'data';

    public function __construct(
        private readonly Client $client,
        private readonly LoggerInterface $logger
    ) {
    }

    public function getDescriptionByIsbn(BookIsbn $isbn): ?BookDescription
    {
        $isbnValue = $isbn->value();
        
        try {
            $bookData = $this->fetchBookDataFromApi($isbnValue);
            
            if ($bookData === null) {
                $this->logBookNotFoundInExternalApi($isbnValue);
                return null;
            }

            return $this->extractDescriptionFromBookData($bookData);
        } catch (GuzzleException $e) {
            $this->logApiRequestFailure($isbnValue, $e);
            return null;
        } catch (Throwable $e) {
            $this->logUnexpectedError($isbnValue, $e);
            return null;
        }
    }

    /**
     * Realiza la petición HTTP a la API de OpenLibrary
     */
    private function fetchBookDataFromApi(string $isbn): ?array
    {
        $response = $this->client->request('GET', self::API_ENDPOINT, [
            'query' => [
                'bibkeys' => sprintf('ISBN:%s', $isbn),
                'format' => self::API_FORMAT,
                'jscmd' => self::API_COMMAND,
            ],
            'timeout' => 5,
        ]);

        $responseBody = $response->getBody()->getContents();
        $decodedData = json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
        
        $lookupKey = sprintf('ISBN:%s', $isbn);
        
        return $decodedData[$lookupKey] ?? null;
    }

    /**
     * Extrae la descripción del libro desde los datos de la API
     * 
     * Nota: La API de OpenLibrary no siempre proporciona el campo 'description',
     * por lo que utilizamos el 'title' como fallback para enriquecer el libro.
     */
    private function extractDescriptionFromBookData(array $bookData): ?BookDescription
    {
        // Intentar obtener descripción oficial si está disponible
        $descriptionText = $bookData['description'] ?? $bookData['title'] ?? null;

        if (empty($descriptionText)) {
            return null;
        }

        // Si la descripción es un objeto con 'value', extraerlo
        if (is_array($descriptionText) && isset($descriptionText['value'])) {
            $descriptionText = $descriptionText['value'];
        }

        return BookDescription::fromString((string) $descriptionText);
    }

    private function logBookNotFoundInExternalApi(string $isbn): void
    {
        $this->logger->info(
            'OpenLibrary API returned no data for the requested ISBN',
            ['isbn' => $isbn, 'source' => 'OpenLibrary']
        );
    }

    private function logApiRequestFailure(string $isbn, GuzzleException $exception): void
    {
        $this->logger->error(
            'Failed to retrieve book information from OpenLibrary API',
            [
                'isbn' => $isbn,
                'error' => $exception->getMessage(),
                'error_type' => 'api_request_failed',
            ]
        );
    }

    private function logUnexpectedError(string $isbn, Throwable $exception): void
    {
        $this->logger->error(
            'Unexpected error while processing OpenLibrary API response',
            [
                'isbn' => $isbn,
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]
        );
    }
}