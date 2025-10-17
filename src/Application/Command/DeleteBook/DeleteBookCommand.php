<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteBook;

final readonly class DeleteBookCommand
{
    public function __construct(
        private string $isbn
    ) {
    }

    public function isbn(): string
    {
        return $this->isbn;
    }
}
