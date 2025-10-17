<?php
declare(strict_types=1);

namespace App\Domain\Book\Service;

use App\Domain\Book\ValueObject\BookDescription;
use App\Domain\Book\ValueObject\BookIsbn;

interface BookDescriptionProviderInterface
{
    public function getDescriptionByIsbn(BookIsbn $isbn): ?BookDescription;
}