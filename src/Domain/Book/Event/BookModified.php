<?php

declare(strict_types=1);

namespace App\Domain\Book\Event;

use App\Domain\Book\ValueObject\BookIsbn;
use App\Domain\Book\ValueObject\BookTitle;

/**
 * Domain Event: BookModified
 * 
 * Representa un cambio en el ciclo de vida de un libro dentro del dominio.
 * Este evento se dispara cuando:
 * - Se crea un nuevo libro (action: 'created')
 * - Se actualiza un libro existente (action: 'updated')
 * - Se elimina un libro (action: 'deleted')
 * 
 * Como evento de dominio, es inmutable y sigue el patrón Event Sourcing,
 * permitiendo que diferentes partes de la aplicación reaccionen a estos cambios
 * sin acoplamiento directo (ej: logging, notificaciones, invalidación de cache).
 * 
 * @see BookModifiedListener para el handler principal de este evento
 */
final readonly class BookModified
{
    private function __construct(
        public BookTitle $title,
        public BookIsbn $isbn,
        public string $action
    ) {
    }

    /**
     * Factory method: Crea un evento de libro creado
     */
    public static function created(BookTitle $title, BookIsbn $isbn): self
    {
        return new self($title, $isbn, 'created');
    }

    /**
     * Factory method: Crea un evento de libro actualizado
     */
    public static function updated(BookTitle $title, BookIsbn $isbn): self
    {
        return new self($title, $isbn, 'updated');
    }

    /**
     * Factory method: Crea un evento de libro eliminado
     */
    public static function deleted(BookTitle $title, BookIsbn $isbn): self
    {
        return new self($title, $isbn, 'deleted');
    }
}
