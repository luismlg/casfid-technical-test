<?php

declare(strict_types=1);

namespace App\Domain\Book\ValueObject;

use App\Domain\Shared\Stringable;
use App\Domain\Book\Exception\InvalidBookTitleException;

/**
 * BookTitle Value Object
 * 
 * Representa el título de un libro como un value object inmutable.
 * Garantiza que el título cumple con las reglas de negocio:
 * - No puede estar vacío
 * - Debe tener al menos 1 carácter (después de trim)
 * - No puede exceder 255 caracteres
 * 
 * Patrón: Value Object del Domain-Driven Design
 */
final class BookTitle implements Stringable
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
            throw new InvalidBookTitleException('Title cannot be empty');
        }

        if (strlen($trimmed) < self::MIN_LENGTH) {
            throw new InvalidBookTitleException(
                sprintf('Title must be at least %d characters long', self::MIN_LENGTH)
            );
        }

        if (strlen($trimmed) > self::MAX_LENGTH) {
            throw new InvalidBookTitleException(
                sprintf('Title cannot exceed %d characters', self::MAX_LENGTH)
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
