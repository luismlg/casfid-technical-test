<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Book\Book;
use App\Domain\Book\BookCollection;
use App\Domain\Book\BookRepositoryInterface;
use App\Domain\Book\ValueObject\BookIsbn;

final class FakeBookRepository implements BookRepositoryInterface
{
    /** @var array<string, Book> */
    private array $books = [];

    public function save(Book $book): void
    {
        $this->books[$book->isbn()->value()] = $book;
    }

    public function findByIsbn(BookIsbn $isbn): ?Book
    {
        return $this->books[$isbn->value()] ?? null;
    }

    public function exists(BookIsbn $isbn): bool
    {
        return isset($this->books[$isbn->value()]);
    }

    public function delete(BookIsbn $isbn): void
    {
        unset($this->books[$isbn->value()]);
    }

    public function findAll(): BookCollection
    {
        return new BookCollection(array_values($this->books));
    }

    public function search(string $term): BookCollection
    {
        $searchTerm = strtolower($term);
        $results = [];

        foreach ($this->books as $book) {
            $titleMatch = str_contains(strtolower($book->title()->value()), $searchTerm);
            $authorMatch = str_contains(strtolower($book->author()->value()), $searchTerm);
            $descriptionMatch = $book->description() !== null 
                && str_contains(strtolower($book->description()->value()), $searchTerm);

            if ($titleMatch || $authorMatch || $descriptionMatch) {
                $results[] = $book;
            }
        }

        return new BookCollection($results);
    }

    /**
     * Helper method for tests: clear all books
     */
    public function clear(): void
    {
        $this->books = [];
    }

    /**
     * Helper method for tests: get count
     */
    public function count(): int
    {
        return count($this->books);
    }
}
