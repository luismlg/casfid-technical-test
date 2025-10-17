<?php

declare(strict_types=1);

namespace Tests\MotherObject\Book\ValueObject;

use App\Domain\Book\ValueObject\BookTitle;

final class BookTitleMotherObject
{
    public static function create(?string $value = null): BookTitle
    {
        return BookTitle::fromString($value ?? self::random());
    }

    public static function random(): string
    {
        $titles = [
            'Clean Code',
            'The Pragmatic Programmer',
            'Design Patterns',
            'Refactoring',
            'Domain-Driven Design',
            'The Clean Coder',
            'Code Complete',
            'The Mythical Man-Month',
            'Structure and Interpretation of Computer Programs',
            'Introduction to Algorithms',
        ];

        return $titles[array_rand($titles)];
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
