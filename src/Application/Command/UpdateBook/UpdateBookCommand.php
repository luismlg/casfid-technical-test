<?php

declare(strict_types=1);

namespace App\Application\Command\UpdateBook;

final readonly class UpdateBookCommand
{
    public function __construct(
        private string $isbn,
        private ?string $title = null,
        private ?string $author = null,
        private ?int $year = null,
        private ?string $description = null,
        private ?string $coverUrl = null
    ) {
    }

    public function isbn(): string
    {
        return $this->isbn;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function author(): ?string
    {
        return $this->author;
    }

    public function year(): ?int
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
