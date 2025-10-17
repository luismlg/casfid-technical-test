<?php

declare(strict_types=1);

namespace App\Infrastructure\DataTransformer;

use App\Domain\Book\Book;
use App\Domain\Book\ValueObject\BookTitle;
use App\Domain\Book\ValueObject\BookAuthor;
use App\Domain\Book\ValueObject\BookIsbn;
use App\Domain\Book\ValueObject\BookYear;
use App\Domain\Book\ValueObject\BookDescription;

final class BookDataTransformer
{
    public static function fromArray(array $data): Book
    {
        $description = null;
        if (isset($data['description']) && !empty($data['description'])) {
            $description = BookDescription::fromString($data['description']);
        }

        return new Book(
            BookTitle::fromString($data['title']),
            BookAuthor::fromString($data['author']),
            BookIsbn::fromString($data['isbn']),
            BookYear::fromInt((int) $data['year']),
            $description,
            $data['cover_url'] ?? null
        );
    }

    public static function toArray(Book $book): array
    {
        return [
            'title' => $book->title()->value(),
            'author' => $book->author()->value(),
            'isbn' => $book->isbn()->value(),
            'year' => $book->year()->value(),
            'description' => $book->description()?->value(),
            'cover_url' => $book->coverUrl(),
        ];
    }
}
