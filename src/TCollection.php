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
  protected $items = [];
  /** @var string Type of items in the collection */
  protected $class;
  /** @var string|NULL */
  protected $uniqueProperty = NULL;
  
  /**
   * @return int
   */
  public function count(): int {
    return count($this->items);
  }
  
  /**
   * @return \ArrayIterator
   */
  public function getIterator(): \ArrayIterator {
    return new \ArrayIterator($this->items);
  }
  
  /**
   * @param int $index
   * @return bool
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
   * @return bool
   */
  protected function checkUniqueness($newItem): bool {
    if(is_null($this->uniqueProperty)) {
      return true;
    }
    foreach($this as $item) {
      if($newItem->{$this->uniqueProperty} === $item->{$this->uniqueProperty}) {
        return false;
      }
    }
    return true;
  }
  
  /**
   * @param int|NULL $index
   * @param object $item
   * @return void
   * @throws \OutOfRangeException
   * @throws \InvalidArgumentException
   */
  public function offsetSet($index, $item): void {
    if(!$item instanceof $this->class) {
      throw new \InvalidArgumentException("Argument must be of $this->class type.");
    } elseif(!$this->checkUniqueness($item)) {
      $property = $this->uniqueProperty;
      throw new \RuntimeException("Duplicate $property {$item->$property}.");
    }
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
   * @return void
   * @throws \OutOfRangeException
   */
  public function offsetUnset($index): void {
    if($index < 0 OR $index >= count($this->items)) {
      throw new \OutOfRangeException("Offset invalid or out of range.");
    }
    array_splice($this->items, $index, 1);
  }
}
?>