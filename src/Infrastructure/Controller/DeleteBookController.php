<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Command\DeleteBook\DeleteBook;
use App\Application\Command\DeleteBook\DeleteBookCommand;
use App\Domain\Book\Exception\BookNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class DeleteBookController
{
    public function __construct(
        private readonly DeleteBook $useCase
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $isbn = $request->attributes->get('isbn');

        if (empty($isbn)) {
            return new JsonResponse(
                ['error' => 'ISBN is required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $command = new DeleteBookCommand($isbn);
            $this->useCase->execute($command);

            return new JsonResponse(
                ['message' => 'Book deleted successfully'],
                Response::HTTP_OK
            );
        } catch (BookNotFoundException $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        } catch (Throwable $e) {
            return new JsonResponse(
                ['error' => 'Internal server error', 'details' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
