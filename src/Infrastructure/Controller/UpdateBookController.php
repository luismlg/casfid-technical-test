<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Command\UpdateBook\UpdateBook;
use App\Application\Command\UpdateBook\UpdateBookCommand;
use App\Domain\Book\Exception\BookNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;
use Throwable;

final class UpdateBookController
{
    public function __construct(
        private readonly UpdateBook $useCase
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $isbn = $request->attributes->get('isbn');
        $body = json_decode($request->getContent(), true) ?? [];

        if (empty($isbn)) {
            return new JsonResponse(
                ['error' => 'ISBN is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $command = new UpdateBookCommand(
                isbn: $isbn,
                title: $body['title'] ?? null,
                author: $body['author'] ?? null,
                year: isset($body['year']) ? (int) $body['year'] : null,
                description: $body['description'] ?? null,
                coverUrl: $body['cover_url'] ?? null
            );

            $this->useCase->execute($command);

            return new JsonResponse(
                ['message' => 'Book updated successfully'],
                Response::HTTP_OK
            );
        } catch (BookNotFoundException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_NOT_FOUND
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
