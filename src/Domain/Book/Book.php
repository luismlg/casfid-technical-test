<?php

declare(strict_types=1);

namespace App\Domain\Book;

use App\Domain\Book\ValueObject\BookTitle;
use App\Domain\Book\ValueObject\BookAuthor;
use App\Domain\Book\ValueObject\BookIsbn;
use App\Domain\Book\ValueObject\BookYear;
use App\Domain\Book\ValueObject\BookDescription;

/**
 * Book - Entidad de Dominio (Aggregate Root)
 * 
 * Representa un libro en el sistema. Esta es la entidad raíz del agregado Book,
 * siguiendo los principios de Domain-Driven Design.
 * 
 * Características:
 * - El ISBN es inmutable (identificador natural del libro)
 * - Todos los demás atributos son mutables a través de métodos específicos
 * - Utiliza Value Objects para encapsular lógica de validación
 * - No expone setters genéricos, solo métodos de actualización semánticos
 * 
 * Atributos:
 * @property BookTitle $title - Título del libro
 * @property BookAuthor $author - Autor principal
 * @property BookIsbn $isbn - ISBN (identificador único e inmutable)
 * @property BookYear $year - Año de publicación
 * @property BookDescription|null $description - Sinopsis (opcional)
 * @property string|null $coverUrl - URL de la portada (opcional)
 */
final class Book
{
    public function __construct(
        private BookTitle $title,
        private BookAuthor $author,
        private readonly BookIsbn $isbn,
        private BookYear $year,
        private ?BookDescription $description = null,
        private ?string $coverUrl = null
    ) {
    }

    public function title(): BookTitle
    {
        return $this->title;
    }

    public function author(): BookAuthor
    {
        return $this->author;
    }

    public function isbn(): BookIsbn
    {
        return $this->isbn;
    }

    public function year(): BookYear
    {
        return $this->year;
    }

    public function description(): ?BookDescription
    {
        return $this->description;
    }

    public function coverUrl(): ?string
    {
        return $this->coverUrl;
    }

    public function updateTitle(BookTitle $title): void
    {
        $this->title = $title;
    }

    public function updateAuthor(BookAuthor $author): void
    {
        $this->author = $author;
    }

    public function updateYear(BookYear $year): void
    {
        $this->year = $year;
    }

    public function updateDescription(?BookDescription $description): void
    {
        $this->description = $description;
    }

    public function updateCoverUrl(?string $coverUrl): void
    {
        $this->coverUrl = $coverUrl;
    }
}
