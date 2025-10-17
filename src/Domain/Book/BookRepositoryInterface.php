<?php

declare(strict_types=1);

namespace App\Domain\Book;

use App\Domain\Book\ValueObject\BookIsbn;

interface BookRepositoryInterface
{
    public function save(Book $book): void;

    public function findByIsbn(BookIsbn $isbn): ?Book;

    public function exists(BookIsbn $isbn): bool;

    public function delete(BookIsbn $isbn): void;

    public function findAll(): BookCollection;

    /**
     * @param string $search
     * @return BookCollection
     */
    public function search(string $search): BookCollection;
}
