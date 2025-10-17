<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Domain\Book\BookCollection;

final readonly class BookCollectionDto
{
    /**
     * @param array<BookDto> $books
     */
    private function __construct(
        private array $books
    ) {
    }

    public static function fromBookCollection(BookCollection $bookCollection): self
    {
        $books = [];
        foreach ($bookCollection as $book) {
            $books[] = BookDto::fromBook($book);
        }

        return new self($books);
    }

    public function toArray(): array
    {
        return array_map(
            fn(BookDto $book) => $book->toArray(),
            $this->books
        );
    }

    public function count(): int
    {
        return count($this->books);
    }

    public function isEmpty(): bool
    {
        return empty($this->books);
    }
}
