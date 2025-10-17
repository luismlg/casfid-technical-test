<?php

declare(strict_types=1);

namespace App\Application\Query\GetBooks;

final readonly class GetBooksQuery
{
    public function __construct(
        private ?string $search = null
    ) {
    }

    public function search(): ?string
    {
        return $this->search;
    }

    public function hasSearch(): bool
    {
        return $this->search !== null && trim($this->search) !== '';
    }
}
