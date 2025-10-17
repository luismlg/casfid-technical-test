<?php

declare(strict_types=1);

namespace App\Domain\Book\Exception;

use DomainException;

/**
 * BookAlreadyExistsException
 * 
 * Excepción de dominio que se lanza cuando se intenta crear un libro
 * cuyo ISBN ya existe en el sistema.
 * 
 * Esta excepción protege la regla de negocio: "Un ISBN es único por libro"
 */
class BookAlreadyExistsException extends DomainException
{
    public static function withIsbn(string $isbn): self
    {
        return new self(sprintf('Book with ISBN "%s" already exists', $isbn));
    }
}
