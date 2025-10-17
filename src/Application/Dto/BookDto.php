<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Domain\Book\Book;

/**
 * BookDto - Data Transfer Object
 * 
 * DTO utilizado para transferir datos de libros entre capas de la aplicación.
 * Convierte entidades de dominio (Book) en estructuras de datos simples para:
 * - Respuestas JSON de la API
 * - Serialización
 * - Transporte entre capas (Application <-> Infrastructure)
 * 
 * Ventajas:
 * - Desacopla la capa de dominio de la representación externa
 * - Facilita la evolución independiente del modelo de dominio y la API
 * - Proporciona control explícito sobre qué datos se exponen
 */
final readonly class BookDto
{
    public function __construct(
        public string $isbn,
        public string $title,
        public string $author,
        public int $year,
        public ?string $description = null,
        public ?string $coverUrl = null,
    ) {
    }

    public static function fromBook(Book $book): self
    {
        return new self(
            isbn: $book->isbn()->value(),
            title: $book->title()->value(),
            author: $book->author()->value(),
            year: $book->year()->value(),
            description: $book->description()?->value(),
            coverUrl: $book->coverUrl(),
        );
    }

    public function toArray(): array
    {
        return [
            'isbn' => $this->isbn,
            'title' => $this->title,
            'author' => $this->author,
            'year' => $this->year,
            'description' => $this->description,
            'cover_url' => $this->coverUrl,
        ];
    }
}
