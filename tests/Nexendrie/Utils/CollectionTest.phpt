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
      $item = $this->col[1];
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
}

$test = new CollectionTest;
$test->run();
?>