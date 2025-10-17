<?php

declare(strict_types=1);

namespace App\Domain\Book\ValueObject;

use App\Domain\Shared\Stringable;
use App\Domain\Book\Exception\InvalidBookIsbnException;

/**
 * BookIsbn Value Object
 * 
 * Representa el ISBN (International Standard Book Number) como un value object inmutable.
 * Soporta tanto ISBN-10 como ISBN-13 y valida el formato según los estándares internacionales.
 * 
 * Reglas de validación:
 * - ISBN-10: Exactamente 10 caracteres (9 dígitos + 1 dígito de control o 'X')
 * - ISBN-13: Exactamente 13 dígitos
 * - Permite guiones y espacios que serán normalizados internamente
 * 
 * Ejemplos válidos: "978-0132350884", "0132350882", "978-3-16-148410-0"
 * 
 * @see https://en.wikipedia.org/wiki/ISBN
 */
final class BookIsbn implements Stringable
{
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
        $cleaned = preg_replace('/[\s\-]/', '', $value);

        if (empty($cleaned)) {
            throw new InvalidBookIsbnException('ISBN cannot be empty');
        }

        // Validar ISBN-10 o ISBN-13
        if (!$this->isValidIsbn10($cleaned) && !$this->isValidIsbn13($cleaned)) {
            throw new InvalidBookIsbnException(
                'ISBN must be a valid ISBN-10 or ISBN-13 format'
            );
        }
    }

    private function isValidIsbn10(string $isbn): bool
    {
        if (strlen($isbn) !== 10) {
            return false;
        }

        return (bool) preg_match('/^\d{9}[\dX]$/', $isbn);
    }

    private function isValidIsbn13(string $isbn): bool
    {
        if (strlen($isbn) !== 13) {
            return false;
        }

        return (bool) preg_match('/^\d{13}$/', $isbn);
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
