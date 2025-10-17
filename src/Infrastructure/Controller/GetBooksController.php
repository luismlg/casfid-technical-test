<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Query\GetBooks\GetBooks;
use App\Application\Query\GetBooks\GetBooksQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class GetBooksController
{
    public function __construct(
        private readonly GetBooks $useCase
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $getBooksQuery = new GetBooksQuery(
                search: $request->query->get('search')
            );

            $response = $this->useCase->execute($getBooksQuery);

            return new JsonResponse(
                [
                    'data' => $response->toArray(),
                    'count' => $response->books()->count()
                ],
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            return new JsonResponse(
                ['error' => 'Internal server error', 'details' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
