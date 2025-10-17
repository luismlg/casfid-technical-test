<?php

declare(strict_types=1);

namespace App\Application\Query\GetBooks;

use App\Application\Dto\BookCollectionDto;
use App\Domain\Book\BookRepositoryInterface;

final class GetBooks
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository
    ) {
    }

    public function execute(GetBooksQuery $query): GetBooksResponse
    {
        if ($query->hasSearch()) {
            $books = $this->bookRepository->search($query->search());
        } else {
            $books = $this->bookRepository->findAll();
        }

        return new GetBooksResponse(
            BookCollectionDto::fromBookCollection($books)
        );
    }
}
