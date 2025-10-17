<?php

declare(strict_types=1);

namespace Tests\MotherObject\Book\ValueObject;

use App\Domain\Book\ValueObject\BookYear;

final class BookYearMotherObject
{
    public static function create(?int $value = null): BookYear
    {
        return BookYear::fromInt($value ?? self::random());
    }

    public static function random(): int
    {
        $years = [1990, 1995, 2000, 2005, 2008, 2010, 2015, 2020, 2022, 2024];
        return $years[array_rand($years)];
    }

    public static function current(): int
    {
        return (int) date('Y');
    }

    public static function tooOld(): int
    {
        return 999;
    }

    public static function future(): int
    {
        return (int) date('Y') + 2;
    }

    public static function valid(): int
    {
        return 2008;
    }
}
