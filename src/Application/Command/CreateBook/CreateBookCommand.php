<?php

declare(strict_types=1);

namespace App\Application\Command\CreateBook;

final readonly class CreateBookCommand
{
    public function __construct(
        private string $title,
        private string $author,
        private string $isbn,
        private int $year,
        private ?string $description = null,
        private ?string $coverUrl = null
    ) {
    }

    public function title(): string
    {
        return $this->title;
    }

    public function author(): string
    {
        return $this->author;
    }

    public function isbn(): string
    {
        return $this->isbn;
    }

    public function year(): int
    {
        return $this->year;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function coverUrl(): ?string
    {
        return $this->coverUrl;
    }
}
