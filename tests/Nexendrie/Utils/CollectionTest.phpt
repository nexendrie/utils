<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

final class CollectionTest extends \Tester\TestCase {
  /** @var Collection */
  protected $col;
  
  public function setUp(): void {
    $this->col = new TestCollection;
  }
  
  public function testCount(): void {
    Assert::same(0, count($this->col));
    $this->col[] = new Item("Item 1");
    Assert::count(1, $this->col);
  }
  
  public function testGetIterator() {
    for($i = 1; $i <= 5; $i++) {
      $this->col[] = new Item("Item 1");
    }
    /** @var Item $item */
    foreach($this->col as $item) {
      Assert::same("Item 1", $item->var1);
    }
  }
  
  public function testOffsetExists() {
    Assert::false(isset($this->col[0]));
    $this->col[] = new Item("Item 1");
    Assert::true(isset($this->col[0]));
  }
  
  public function testOffsetGet() {
    $this->col[] = new Item("Item 1");
    $item = $this->col[0];
    Assert::type(Item::class, $item);
    Assert::exception(function() {
      $this->col[1];
    }, \OutOfRangeException::class);
  }
  
  public function testOffsetSet() {
    $this->col[] = new Item("Item 1");
    $this->col[0] = new Item("Item 2");
    Assert::same("Item 2", $this->col[0]->var1);
    Assert::exception(function() {
      $this->col[] = new \stdClass;
    }, \InvalidArgumentException::class);
    Assert::exception(function() {
      $this->col[-1] = new Item("Item 1");
    }, \OutOfRangeException::class);
  }
  
  public function testUniqueness() {
    $col = new UniqueCollection;
    $col[] = new Item("Item 1");
    Assert::exception(function() use($col) {
      $col[] = new Item("Item 1");
    }, \RuntimeException::class, "Duplicate var1 Item 1.");
  }
  
  public function testOffsetUnset() {
    $this->col[] = new Item("Item 1");
    unset($this->col[0]);
    Assert::false(isset($this->col[0]));
    Assert::exception(function() {
      unset($this->col[0]);
    }, \OutOfRangeException::class);
  }
  
  public function testLocking() {
    $this->col[] = new Item("Item 1");
    $this->col->lock();
    Assert::true($this->col->isLocked());
    Assert::exception(function() {
      $this->col[] = new Item("Item 1");
    }, \RuntimeException::class);
    Assert::exception(function() {
      unset($this->col[0]);
    }, \RuntimeException::class);
  }
  
  public function testMaxSize() {
    $col = new MaxSizedCollection();
    $col[] = new Item("Item 1");
    Assert::exception(function() use($col) {
      $col[] = new Item("Item 1");
    }, \RuntimeException::class, "Collection reached its max size. Cannot add more items.");
  }
  
  public function testCheckers() {
    $col = new TestCollection();
    $col->addChecker(function(Item $item) {
      if($item->var1 === "Item 2") {
        throw new \RuntimeException("");
      }
    });
    $col[] = new Item("Item 1");
    Assert::exception(function() use($col) {
      $col[] = new Item("Item 2");
    }, \RuntimeException::class, "");
  }
  
  public function testToArray() {
    $this->col[] = new Item("Item 1");
    $this->col[] = new Item("Item 2");
    $array = $this->col->toArray();
    Assert::type("array", $array);
    Assert::count(2, $array);
  }
  
  public function testHasItems() {
    $this->col[] = new Item("Item 1");
    $this->col[] = new Item("Item 2");
    Assert::true($this->col->hasItems());
    Assert::true($this->col->hasItems(["var1" => "Item 1"]));
    Assert::false($this->col->hasItems(["var1" => "Item 3"]));
  }
  
  public function testGetItems() {
    Assert::count(0, $this->col->getItems());
    $this->col[] = new Item("Item 1");
    $this->col[] = new Item("Item 2");
    Assert::count(2, $this->col->getItems());
    Assert::count(1, $this->col->getItems(["var1" => "Item 1"]));
    Assert::count(0, $this->col->getItems(["var1" => "Item 3"]));
  }
}

$test = new CollectionTest;
$test->run();
?>