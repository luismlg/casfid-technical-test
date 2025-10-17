<?php

declare(strict_types=1);

namespace App\Domain\Book\ValueObject;

use App\Domain\Shared\Stringable;
use App\Domain\Book\Exception\InvalidBookAuthorException;

/**
 * BookAuthor Value Object
 * 
 * Representa el autor de un libro como un value object inmutable.
 * Encapsula las reglas de validación para nombres de autores:
 * - No puede estar vacío
 * - Longitud mínima: 1 carácter (después de trim)
 * - Longitud máxima: 255 caracteres
 * 
 * Nota: Soporta nombres de autores en cualquier idioma, incluyendo caracteres Unicode.
 * Ejemplos: "Gabriel García Márquez", "村上春樹" (Haruki Murakami), "J.K. Rowling"
 */
final class BookAuthor implements Stringable
{
    private const MIN_LENGTH = 1;
    private const MAX_LENGTH = 255;

    private string $value;

    private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    private function validate(string $value): void
    {
        $trimmed = trim($value);
        
        if (empty($trimmed)) {
            throw new InvalidBookAuthorException('Author cannot be empty');
        }

        if (strlen($trimmed) < self::MIN_LENGTH) {
            throw new InvalidBookAuthorException(
                sprintf('Author must be at least %d characters long', self::MIN_LENGTH)
            );
        }

        if (strlen($trimmed) > self::MAX_LENGTH) {
            throw new InvalidBookAuthorException(
                sprintf('Author cannot exceed %d characters', self::MAX_LENGTH)
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
