<?php

declare(strict_types=1);

namespace Tests\MotherObject\Book\ValueObject;

use App\Domain\Book\ValueObject\BookDescription;

final class BookDescriptionMotherObject
{
    public static function create(?string $value = null): ?BookDescription
    {
        if ($value === null) {
            return null;
        }

        return BookDescription::fromString($value);
    }

    public static function random(): string
    {
        $descriptions = [
            'A comprehensive guide to writing clean and maintainable code.',
            'Essential reading for any software developer looking to improve their craft.',
            'Practical advice and timeless principles for professional programmers.',
            'An in-depth exploration of software design patterns and best practices.',
            'Learn how to tackle complexity in the heart of software.',
            'A must-read book about professional software development.',
            'Timeless wisdom about software engineering and project management.',
            'The definitive guide to object-oriented design patterns.',
            'A classic text on computer science fundamentals.',
            'Comprehensive coverage of algorithms and data structures.',
        ];

        return $descriptions[array_rand($descriptions)];
    }

    public static function randomBookDescription(): BookDescription
    {
        return BookDescription::fromString(self::random());
    }

    public static function empty(): string
    {
        return '';
    }

    public static function tooLong(): string
    {
        return str_repeat('a', 5001);
    }

    public static function valid(): string
    {
        return 'A handbook of agile software craftsmanship.';
    }
}
