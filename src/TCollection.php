<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * TCollection
 * Target class has to implement \ArrayAccess, \Countable, \IteratorAggregate interfaces
 *
 * @author Jakub Konečný
 */
trait TCollection {
  /** @var object[] */
  protected $items = [];
  /** @var string Type of items in the collection */
  protected $class;
  /** @var string|NULL */
  protected $uniqueProperty = null;
  /** @var int */
  protected $maxSize = 0;
  /** @var bool */
  protected $locked = false;
  /** @var callable[] */
  protected $checkers = [];
  
  public function __construct() {
    $this->addChecker([$this, "checkLock"]);
    $this->addChecker([$this, "checkType"]);
    $this->addChecker([$this, "checkUniqueness"]);
    $this->addChecker([$this, "checkSize"]);
  }
  
  public function isLocked(): bool {
    return $this->locked;
  }
  
  public function lock(): void {
    $this->locked = true;
  }
  
  public function count(): int {
    return count($this->items);
  }
  
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }
  
  /**
   * @param int $index
   */
  public function offsetExists($index): bool {
    return $index >= 0 AND $index < count($this->items);
  }
  
  /**
   * @param int|NULL $index
   * @return object
   * @throws \OutOfRangeException
   */
  public function offsetGet($index) {
    if($index < 0 OR $index >= count($this->items)) {
      throw new \OutOfRangeException("Offset invalid or out of range.");
    }
    return $this->items[$index];
  }
  
  public function addChecker(callable $checker): void {
    $this->checkers[] = $checker;
  }
  
  /**
   * @param object $newItem
   */
  protected function checkLock($newItem, self $collection): void {
    if($collection->locked) {
      throw new \RuntimeException("Cannot add items to locked collection.");
    }
  }
  
  /**
   * @param object $newItem
   */
  protected function checkType($newItem, self $collection): void {
    if(!$newItem instanceof $collection->class) {
      throw new \InvalidArgumentException("Argument must be of $this->class type.");
    }
  }
  
  /**
   * @param object $newItem
   */
  protected function checkUniqueness($newItem, self $collection): void {
    $uniqueProperty = $collection->uniqueProperty;
    if(is_null($uniqueProperty)) {
      return;
    }
    if($this->hasItems([$uniqueProperty => $newItem->$uniqueProperty])) {
      throw new \RuntimeException("Duplicate $uniqueProperty {$newItem->$uniqueProperty}.");
    }
  }
  
  /**
   * @param object $newItem
   */
  protected function checkSize($newItem, self $collection): void {
    if($collection->maxSize < 1) {
      return;
    }
    if($collection->count() + 1 > $collection->maxSize) {
      throw new \RuntimeException("Collection reached its max size. Cannot add more items.");
    }
  }
  
  /**
   * @param object $item
   */
  protected function performChecks($item): void {
    foreach($this->checkers as $checker) {
      call_user_func($checker, $item, $this);
    }
  }
  
  /**
   * @param int|NULL $index
   * @param object $item
   * @throws \OutOfRangeException
   * @throws \InvalidArgumentException
   * @throws \RuntimeException
   */
  public function offsetSet($index, $item): void {
    $this->performChecks($item);
    if($index === null) {
      $this->items[] = & $item;
    } elseif($index < 0 OR $index >= count($this->items)) {
      throw new \OutOfRangeException("Offset invalid or out of range.");
    } else {
      $this->items[$index] = & $item;
    }
  }
  
  /**
   * @param int $index
   * @throws \OutOfRangeException
   */
  public function offsetUnset($index): void {
    if($this->locked) {
      throw new \RuntimeException("Cannot remove items from locked collection.");
    } elseif($index < 0 OR $index >= count($this->items)) {
      throw new \OutOfRangeException("Offset invalid or out of range.");
    }
    array_splice($this->items, $index, 1);
  }
  
  public function toArray(): array {
    return $this->items;
  }
  
  /**
   * Create new collection from array
   */
  public static function fromArray(array $items, ...$args): self {
    $collection = new static(...$args);
    foreach($items as $item) {
      $collection[] = $item;
    }
    return $collection;
  }
  
  /**
   * Check if the collection has at least 1 item matching the filter
   */
  public function hasItems(array $filter = []): bool {
    return (count($this->getItems($filter)) > 0);
  }
  
  /**
   * Get all items matching the filter
   */
  public function getItems(array $filter = []): array {
    return Filter::applyFilter($this->items, $filter);
  }
}
?>