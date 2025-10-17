<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Command\CreateBook\CreateBook;
use App\Application\Command\CreateBook\CreateBookCommand;
use App\Domain\Book\Exception\BookAlreadyExistsException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;
use Throwable;

final class CreateBookController
{
    public function __construct(
        private readonly CreateBook $useCase
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $body = json_decode($request->getContent(), true) ?? [];

        // Validar campos requeridos
        if (!isset($body['title']) || empty(trim($body['title']))) {
            return new JsonResponse(
                ['error' => 'Title is required and must be a non-empty string'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!isset($body['author']) || empty(trim($body['author']))) {
            return new JsonResponse(
                ['error' => 'Author is required and must be a non-empty string'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!isset($body['isbn']) || empty(trim($body['isbn']))) {
            return new JsonResponse(
                ['error' => 'ISBN is required and must be a non-empty string'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!isset($body['year']) || !is_numeric($body['year'])) {
            return new JsonResponse(
                ['error' => 'Year is required and must be a valid integer'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $command = new CreateBookCommand(
                title: $body['title'],
                author: $body['author'],
                isbn: $body['isbn'],
                year: (int) $body['year'],
                description: $body['description'] ?? null,
                coverUrl: $body['cover_url'] ?? null
            );

            $this->useCase->execute($command);

            return new JsonResponse(
                ['message' => 'Book created successfully'],
                Response::HTTP_CREATED
            );
        } catch (BookAlreadyExistsException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_CONFLICT
            );
        } catch (InvalidArgumentException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (Throwable $e) {
            return new JsonResponse(
                ['error' => 'Internal server error', 'details' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
