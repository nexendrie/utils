<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

use Nette\Utils\Arrays;

/**
 * TCollection
 * Target class has to implement \ArrayAccess, \Countable, \IteratorAggregate interfaces
 *
 * @author Jakub Konečný
 */
trait TCollection {
  protected $items = [];
  /** @var string Type of items in the collection */
  protected $class;
  /** @var string|NULL */
  protected $uniqueProperty = NULL;
  /** @var int */
  protected $maxSize = 0;
  /** @var bool */
  protected $locked = false;
  
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
   * @throws \OutOfRangeException
   */
  public function offsetGet($index) {
    if($index < 0 OR $index >= count($this->items)) {
      throw new \OutOfRangeException("Offset invalid or out of range.");
    }
    return $this->items[$index];
  }
  
  /**
   * @param object $newItem
   */
  protected function checkType($newItem): bool {
    return ($newItem instanceof $this->class);
  }
  
  /**
   * @param object $newItem
   */
  protected function checkUniqueness($newItem): bool {
    if(is_null($this->uniqueProperty)) {
      return true;
    }
    return Arrays::every($this->items, function($value) use($newItem) {
      return ($newItem->{$this->uniqueProperty} !== $value->{$this->uniqueProperty});
    });
  }
  
  protected function checkSize(): bool {
    if($this->maxSize < 1) {
      return true;
    }
    return ($this->count() + 1 <= $this->maxSize);
  }
  
  /**
   * @param object $item
   */
  protected function performChecks($item): void {
    if($this->locked) {
      throw new \RuntimeException("Cannot add items to locked collection.");
    } elseif(!$this->checkType($item)) {
      throw new \InvalidArgumentException("Argument must be of $this->class type.");
    } elseif(!$this->checkUniqueness($item)) {
      $property = $this->uniqueProperty;
      throw new \RuntimeException("Duplicate $property {$item->$property}.");
    } elseif(!$this->checkSize()) {
      throw new \RuntimeException("Collection reached its max size. Cannot add more items.");
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
    if($index === NULL) {
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
}
?>