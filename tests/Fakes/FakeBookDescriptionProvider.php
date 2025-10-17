<?php
declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Book\Service\BookDescriptionProviderInterface;
use App\Domain\Book\ValueObject\BookDescription;
use App\Domain\Book\ValueObject\BookIsbn;
use Tests\MotherObject\Book\ValueObject\BookDescriptionMotherObject;

final class FakeBookDescriptionProvider implements BookDescriptionProviderInterface
{
    /** @var array<string, BookDescription> */
    private array $descriptions = [];

    public function getDescriptionByIsbn(BookIsbn $isbn): ?BookDescription
    {
        return $this->descriptions[$isbn->__toString()] ?? null;
    }

    public function save(BookIsbn $isbn, BookDescription $description = null): void
    {
        $key = $isbn->__toString();
        $this->descriptions[$key] = $description ?? BookDescriptionMotherObject::randomBookDescription();
    }
}