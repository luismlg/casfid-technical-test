<?php

declare(strict_types=1);

namespace App\Domain\Book\ValueObject;

use App\Domain\Book\Exception\InvalidBookYearException;

/**
 * BookYear Value Object
 * 
 * Representa el año de publicación de un libro como un value object inmutable.
 * Implementa validaciones de negocio realistas para años de publicación:
 * - Rango válido: 1000 - 9999 (formatos de 4 dígitos)
 * - No permite años futuros más allá del próximo año (considera pre-publicaciones)
 * 
 * Esta restricción es razonable dado que:
 * - Libros anteriores al año 1000 son extremadamente raros (manuscritos medievales)
 * - Permite registrar libros con fecha de publicación del año siguiente (pre-orders)
 */
final class BookYear
{
    private const MIN_YEAR = 1000;
    private const MAX_YEAR = 9999;

    private int $value;

    private function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    private function validate(int $value): void
    {
        if ($value < self::MIN_YEAR || $value > self::MAX_YEAR) {
            throw new InvalidBookYearException(
                sprintf('Year must be between %d and %d', self::MIN_YEAR, self::MAX_YEAR)
            );
        }

        $currentYear = (int) date('Y');
        if ($value > $currentYear + 1) {
            throw new InvalidBookYearException(
                sprintf('Year cannot be in the future (max: %d)', $currentYear + 1)
            );
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
