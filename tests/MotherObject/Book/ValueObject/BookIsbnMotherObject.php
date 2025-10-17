<?php

declare(strict_types=1);

namespace Tests\MotherObject\Book\ValueObject;

use App\Domain\Book\ValueObject\BookIsbn;

final class BookIsbnMotherObject
{
    public static function create(?string $value = null): BookIsbn
    {
        return BookIsbn::fromString($value ?? self::random());
    }

    public static function random(): string
    {
        $isbns = [
            '978-0132350884', // Clean Code
            '978-0201616224', // The Pragmatic Programmer
            '978-0201633610', // Design Patterns
            '978-0321125217', // Domain-Driven Design
            '978-0137081073', // The Clean Coder
            '978-0735619678', // Code Complete
            '978-0201835953', // The Mythical Man-Month
            '978-0262510875', // SICP
            '978-0132762564', // Introduction to Algorithms
            '978-0134685991', // Effective Java
        ];

        return $isbns[array_rand($isbns)];
    }

    public static function isbn10(): string
    {
        return '0132350882'; // Clean Code ISBN-10
    }

    public static function isbn13(): string
    {
        return '978-0132350884'; // Clean Code ISBN-13
    }

    public static function invalid(): string
    {
        return 'invalid-isbn';
    }

    public static function tooShort(): string
    {
        return '123';
    }

    public static function tooLong(): string
    {
        return '978-0132350884-extra';
    }
}
