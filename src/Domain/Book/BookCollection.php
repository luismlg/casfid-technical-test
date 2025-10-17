<?php

declare(strict_types=1);

namespace App\Domain\Book;

use App\Domain\Shared\Collection;

/**
 * @extends Collection<Book>
 */
final class BookCollection extends Collection
{
    protected function isValidItem($item): bool
    {
        return $item instanceof Book;
    }

    protected function type(): string
    {
        return Book::class;
    }
}
