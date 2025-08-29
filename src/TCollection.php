<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * TCollection
 * Target class has to implement \ArrayAccess, \Countable, \IteratorAggregate interfaces
 *
 * @author Jakub Konečný
 */
trait TCollection
{
    /** @var object[] */
    protected array $items = [];
    /** @var string Type of items in the collection */
    protected string $class;
    protected ?string $uniqueProperty = null;
    protected int $maxSize = 0;
    protected bool $locked = false;
    /** @var callable[] */
    protected array $checkers = [];

    public function __construct()
    {
        $this->addDefaultCheckers();
    }

    protected function addDefaultCheckers(): void
    {
        $this->addChecker([$this, "checkLock"]);
        $this->addChecker([$this, "checkType"]);
        $this->addChecker([$this, "checkUniqueness"]);
        $this->addChecker([$this, "checkSize"]);
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function lock(): void
    {
        $this->locked = true;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @param int $index
     */
    public function offsetExists($index): bool
    {
        return $index >= 0 && $index < count($this->items);
    }

    /**
     * @param int|NULL $index
     * @return object
     * @throws \OutOfRangeException
     */
    public function offsetGet($index): mixed
    {
        if ($index < 0 || $index >= count($this->items)) {
            throw new \OutOfRangeException("Offset invalid or out of range.");
        }
        return $this->items[$index];
    }

    public function addChecker(callable $checker): void
    {
        $this->checkers[] = $checker;
    }

    protected function checkLock(object $newItem, self $collection): void
    {
        if ($collection->isLocked()) {
            throw new \RuntimeException("Cannot add items to locked collection.");
        }
    }

    protected function checkType(object $newItem, self $collection): void
    {
        if (!$newItem instanceof $collection->class) {
            throw new \InvalidArgumentException("Argument must be of $this->class type.");
        }
    }

    protected function checkUniqueness(object $newItem, self $collection): void
    {
        $uniqueProperty = $collection->uniqueProperty;
        if ($uniqueProperty === null) {
            return;
        }
        if ($this->hasItems([$uniqueProperty => $newItem->$uniqueProperty])) {
            throw new \RuntimeException("Duplicate $uniqueProperty {$newItem->$uniqueProperty}.");
        }
    }

    protected function checkSize(object $newItem, self $collection): void
    {
        if ($collection->maxSize < 1) {
            return;
        }
        if ($collection->count() + 1 > $collection->maxSize) {
            throw new \RuntimeException("Collection reached its max size. Cannot add more items.");
        }
    }

    protected function performChecks(object $item): void
    {
        foreach ($this->checkers as $checker) {
            $checker($item, $this);
        }
    }

    /**
     * @param int|NULL $index
     * @param object $item
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function offsetSet($index, $item): void
    {
        $this->performChecks($item);
        if ($index === null) {
            $this->items[] = &$item;
        } elseif ($index < 0 || $index >= count($this->items)) {
            throw new \OutOfRangeException("Offset invalid or out of range.");
        } else {
            $this->items[$index] = &$item;
        }
    }

    /**
     * @param int $index
     * @throws \RuntimeException
     * @throws \OutOfRangeException
     */
    public function offsetUnset($index): void
    {
        if ($this->locked) {
            throw new \RuntimeException("Cannot remove items from locked collection.");
        } elseif ($index < 0 || $index >= count($this->items)) {
            throw new \OutOfRangeException("Offset invalid or out of range.");
        }
        array_splice($this->items, $index, 1);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Create new collection from array
     *
     * @param object[] $items
     * @param mixed ...$args
     */
    public static function fromArray(array $items, ...$args): static
    {
        $collection = new static(...$args);
        foreach ($items as $item) {
            $collection[] = $item;
        }
        return $collection;
    }

    /**
     * Check if the collection has at least $count items matching the filter
     */
    public function hasItems(array $filter = [], int $count = 1): bool
    {
        return (count($this->getItems($filter)) >= $count);
    }

    /**
     * Get all items matching the filter
     */
    public function getItems(array $filter = []): array
    {
        return Filter::applyFilter($this->items, $filter);
    }

    /**
     * Get first item matching the filter
     */
    public function getItem(array $filter): ?object
    {
        $items = $this->getItems($filter);
        if (count($items) === 0) {
            return null;
        }
        return $items[0];
    }

    /**
     * Remove all items matching the filter
     *
     * @throws \RuntimeException
     * @throws \OutOfRangeException
     */
    public function removeByFilter(array $filter): void
    {
        foreach ($this->items as $item) {
            if (Filter::matches($item, $filter)) {
                /** @var int $index */
                $index = $this->getIndex($filter);
                $this->offsetUnset($index);
            }
        }
    }

    /**
     * Get index of first item matching the filter
     */
    public function getIndex(array $filter): ?int
    {
        foreach ($this->items as $index => $item) {
            if (Filter::matches($item, $filter)) {
                return $index;
            }
        }
        return null;
    }
}
