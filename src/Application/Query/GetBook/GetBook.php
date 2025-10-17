<?php

declare(strict_types=1);

namespace App\Application\Query\GetBook;

use App\Application\Dto\BookDto;
use App\Domain\Book\BookRepositoryInterface;
use App\Domain\Book\Exception\BookNotFoundException;
use App\Domain\Book\ValueObject\BookIsbn;

final readonly class GetBook
{
    public function __construct(
        private BookRepositoryInterface $bookRepository
    ) {
    }

    public function execute(GetBookQuery $query): GetBookResponse
    {
        $isbn = BookIsbn::fromString($query->isbn());
        
        $book = $this->bookRepository->findByIsbn($isbn);
        
        if ($book === null) {
            throw BookNotFoundException::withIsbn($isbn->value());
        }

        return new GetBookResponse(
            BookDto::fromBook($book)
        );
    }
}
