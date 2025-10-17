<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;
use App\Domain\Shared\Exception\InvalidCollectionItemException;

/**
 * @template T
 * @implements IteratorAggregate<int, T>
 */
abstract class Collection implements Countable, IteratorAggregate
{
    /** @var array<int, T> */
    private array $items;

    /**
     * @param array<int, T> $items
     */
    public function __construct(array $items)
    {
        $this->items = [];
        
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @param T $item
     */
    protected function add($item): void
    {
        if (!$this->isValidItem($item)) {
            throw new InvalidCollectionItemException(
                sprintf(
                    'Invalid item type. Expected %s, got %s',
                    $this->type(),
                    get_debug_type($item)
                )
            );
        }

        $this->items[] = $item;
    }

    /**
     * @param T $item
     */
    abstract protected function isValidItem($item): bool;

    abstract protected function type(): string;

    /**
     * @return array<int, T>
     */
    public function items(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}
