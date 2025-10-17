<?php

declare(strict_types=1);

namespace Tests\MotherObject\Book\ValueObject;

use App\Domain\Book\ValueObject\BookAuthor;

final class BookAuthorMotherObject
{
    public static function create(?string $value = null): BookAuthor
    {
        return BookAuthor::fromString($value ?? self::random());
    }

    public static function random(): string
    {
        $authors = [
            'Robert C. Martin',
            'Martin Fowler',
            'Eric Evans',
            'Kent Beck',
            'Andrew Hunt',
            'David Thomas',
            'Steve McConnell',
            'Frederick P. Brooks Jr.',
            'Erich Gamma',
            'Gang of Four',
        ];

        return $authors[array_rand($authors)];
    }

    public static function tooShort(): string
    {
        return '';
    }

    public static function tooLong(): string
    {
        return str_repeat('a', 256);
    }
}
