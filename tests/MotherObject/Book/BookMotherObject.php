<?php

declare(strict_types=1);

namespace Tests\MotherObject\Book;

use App\Domain\Book\Book;
use App\Domain\Book\ValueObject\BookAuthor;
use App\Domain\Book\ValueObject\BookDescription;
use App\Domain\Book\ValueObject\BookIsbn;
use App\Domain\Book\ValueObject\BookTitle;
use App\Domain\Book\ValueObject\BookYear;
use Tests\MotherObject\Book\ValueObject\BookAuthorMotherObject;
use Tests\MotherObject\Book\ValueObject\BookDescriptionMotherObject;
use Tests\MotherObject\Book\ValueObject\BookIsbnMotherObject;
use Tests\MotherObject\Book\ValueObject\BookTitleMotherObject;
use Tests\MotherObject\Book\ValueObject\BookYearMotherObject;

final class BookMotherObject
{
    public static function create(
        ?string $title = null,
        ?string $author = null,
        ?string $isbn = null,
        ?int $year = null,
        ?string $description = null,
        ?string $coverUrl = null
    ): Book {
        $bookTitle = $title !== null 
            ? BookTitle::fromString($title) 
            : BookTitleMotherObject::create();

        $bookAuthor = $author !== null 
            ? BookAuthor::fromString($author) 
            : BookAuthorMotherObject::create();

        $bookIsbn = $isbn !== null 
            ? BookIsbn::fromString($isbn) 
            : BookIsbnMotherObject::create();

        $bookYear = $year !== null 
            ? BookYear::fromInt($year) 
            : BookYearMotherObject::create();

        $bookDescription = $description !== null 
            ? BookDescription::fromString($description) 
            : null;

        return new Book(
            $bookTitle,
            $bookAuthor,
            $bookIsbn,
            $bookYear,
            $bookDescription,
            $coverUrl
        );
    }

    public static function withDescription(): Book
    {
        return self::create(
            description: BookDescriptionMotherObject::random()
        );
    }

    public static function withoutDescription(): Book
    {
        return self::create(
            description: null
        );
    }

    public static function cleanCode(): Book
    {
        return self::create(
            title: 'Clean Code',
            author: 'Robert C. Martin',
            isbn: '978-0132350884',
            year: 2008,
            description: 'A handbook of agile software craftsmanship.'
        );
    }

    public static function pragmaticProgrammer(): Book
    {
        return self::create(
            title: 'The Pragmatic Programmer',
            author: 'Andrew Hunt and David Thomas',
            isbn: '978-0201616224',
            year: 1999,
            description: 'From Journeyman to Master.'
        );
    }
}
