<?php

declare(strict_types=1);

namespace App\Application\Query\GetBook;

use App\Application\Dto\BookDto;

final readonly class GetBookResponse
{
    public function __construct(
        private BookDto $book
    ) {
    }

    public function toArray(): array
    {
        return $this->book->toArray();
    }

    public function book(): BookDto
    {
        return $this->book;
    }
}
