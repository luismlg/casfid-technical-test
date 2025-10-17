<?php

declare(strict_types=1);

namespace App\Domain\Shared;

interface Stringable
{
    public function value(): string;
}
