<?php

declare(strict_types=1);

namespace App\Domain\Book\Exception;

use DomainException;

/**
 * BookNotFoundException
 * 
 * Excepción de dominio que se lanza cuando se intenta acceder a un libro
 * que no existe en el sistema (búsqueda por ISBN).
 * 
 * Casos de uso: actualización, eliminación o consulta de un libro inexistente
 */
class BookNotFoundException extends DomainException
{
    public static function withIsbn(string $isbn): self
    {
        return new self(sprintf('Book with ISBN "%s" not found', $isbn));
    }
}
