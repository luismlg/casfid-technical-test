<?php

declare(strict_types=1);

namespace App\Domain\Book\ValueObject;

use App\Domain\Shared\Stringable;
use App\Domain\Book\Exception\InvalidBookDescriptionException;

/**
 * BookDescription Value Object
 * 
 * Representa la descripción o sinopsis de un libro como un value object inmutable.
 * A diferencia de otros value objects del dominio Book, la descripción es opcional
 * y puede estar vacía.
 * 
 * Reglas de negocio:
 * - Puede estar vacía (descripción no disponible o no proporcionada)
 * - Longitud máxima: 5000 caracteres (suficiente para sinopsis detalladas)
 * - Soporta texto enriquecido en formato plain text
 * 
 * Casos de uso típicos:
 * - Descripción manual proporcionada por el usuario
 * - Descripción obtenida automáticamente de APIs externas (OpenLibrary)
 */
final class BookDescription implements Stringable
{
    private const MAX_LENGTH = 5000;

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

    public static function empty(): self
    {
        return new self('');
    }

    private function validate(string $value): void
    {
        if (strlen($value) > self::MAX_LENGTH) {
            throw new InvalidBookDescriptionException(
                sprintf('Description cannot exceed %d characters', self::MAX_LENGTH)
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return empty(trim($this->value));
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
