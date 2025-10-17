<?php

declare(strict_types=1);

namespace App\Application\Query\GetBooks;

use App\Application\Dto\BookCollectionDto;

final readonly class GetBooksResponse
{
    public function __construct(
        private BookCollectionDto $books
    ) {
    }

    public function toArray(): array
    {
        return $this->books->toArray();
    }

    public function books(): BookCollectionDto
    {
        return $this->books;
    }
}
