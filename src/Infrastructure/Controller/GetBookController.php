<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Query\GetBook\GetBook;
use App\Application\Query\GetBook\GetBookQuery;
use App\Domain\Book\Exception\BookNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use InvalidArgumentException;
use Throwable;

final readonly class GetBookController
{
    public function __construct(
        private GetBook $useCase
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
            $getBookQuery = new GetBookQuery($isbn);
            $response = $this->useCase->execute($getBookQuery);

            return new JsonResponse(
                ['data' => $response->toArray()],
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
