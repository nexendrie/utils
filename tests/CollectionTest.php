<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * @author Jakub Konečný
 * @testCase
 */
final class CollectionTest extends \MyTester\TestCase
{
    protected Collection $col;

    public function setUp(): void
    {
        $this->col = new TestCollection();
    }

    public function testCount(): void
    {
        $this->assertSame(0, count($this->col));
        $this->col[] = new Item("Item 1");
        $this->assertCount(1, $this->col);
    }

    public function testGetIterator(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->col[] = new Item("Item 1");
        }
        /** @var Item $item */
        foreach ($this->col as $item) {
            $this->assertSame("Item 1", $item->var1);
        }
    }

    public function testOffsetExists(): void
    {
        $this->assertFalse(isset($this->col[0]));
        $this->col[] = new Item("Item 1");
        $this->assertTrue(isset($this->col[0]));
    }

    public function testOffsetGet(): void
    {
        $this->col[] = new Item("Item 1");
        $item = $this->col[0];
        $this->assertType(Item::class, $item);
        $this->assertThrowsException(function () {
            $item = $this->col[1];
        }, \OutOfRangeException::class);
    }

    public function testOffsetSet(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[0] = new Item("Item 2");
        $this->assertSame("Item 2", $this->col[0]->var1);
        $this->assertThrowsException(function () {
            $this->col[] = new \stdClass();
        }, \InvalidArgumentException::class);
        $this->assertThrowsException(function () {
            $this->col[-1] = new Item("Item 1");
        }, \OutOfRangeException::class);
    }

    public function testUniqueness(): void
    {
        $col = new UniqueCollection();
        $col[] = new Item("Item 1");
        $this->assertThrowsException(static function () use ($col) {
            $col[] = new Item("Item 1");
        }, \RuntimeException::class, "Duplicate var1 Item 1.");
    }

    public function testOffsetUnset(): void
    {
        $this->col[] = new Item("Item 1");
        unset($this->col[0]);
        $this->assertFalse(isset($this->col[0]));
        $this->assertThrowsException(function () {
            unset($this->col[0]);
        }, \OutOfRangeException::class);
    }

    public function testLocking(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col->lock();
        $this->assertTrue($this->col->isLocked());
        $this->assertThrowsException(function () {
            $this->col[] = new Item("Item 1");
        }, \RuntimeException::class);
        $this->assertThrowsException(function () {
            unset($this->col[0]);
        }, \RuntimeException::class);
    }

    public function testMaxSize(): void
    {
        $col = new MaxSizedCollection();
        $col[] = new Item("Item 1");
        $this->assertThrowsException(static function () use ($col) {
            $col[] = new Item("Item 1");
        }, \RuntimeException::class, "Collection reached its max size. Cannot add more items.");
    }

    public function testCheckers(): void
    {
        $col = new TestCollection();
        $col->addChecker(static function (Item $item) {
            if ($item->var1 === "Item 2") {
                throw new \RuntimeException("");
            }
        });
        $col[] = new Item("Item 1");
        $this->assertThrowsException(static function () use ($col) {
            $col[] = new Item("Item 2");
        }, \RuntimeException::class, "");
    }

    public function testToArray(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        $array = $this->col->toArray();
        $this->assertType("array", $array);
        $this->assertCount(2, $array);
    }

    public function testFromArray(): void
    {
        $items = [
            new Item("Item 1"), new Item("Item 2"),
        ];
        $collection = TestCollection::fromArray($items);
        $this->assertType(TestCollection::class, $collection);
        $this->assertCount(2, $collection);
        $collection = ParameterCollection::fromArray($items, "abc");
        $this->assertType(ParameterCollection::class, $collection);
        $this->assertCount(2, $collection);
        $this->assertSame("abc", $collection->name);
    }

    public function testHasItems(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        $this->assertTrue($this->col->hasItems());
        $this->assertTrue($this->col->hasItems(["var1" => "Item 1"]));
        $this->assertFalse($this->col->hasItems(["var1" => "Item 3"]));
        $this->assertFalse($this->col->hasItems(["var1" => "Item 1"], 2));
    }

    public function testGetItems(): void
    {
        $this->assertCount(0, $this->col->getItems());
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        $this->col[] = new Item("Item 3", BasicEnum::DEF);
        $this->assertCount(3, $this->col->getItems());
        $this->assertCount(1, $this->col->getItems(["var1" => "Item 1",]));
        $this->assertCount(0, $this->col->getItems(["var1" => "Item 4",]));
        $this->assertCount(2, $this->col->getItems(["var1<" => "Item 3",]));
        $this->assertCount(2, $this->col->getItems(["var2" => BasicEnum::ABC,]));
        $this->assertCount(1, $this->col->getItems(["var2!=" => BasicEnum::ABC,]));
    }

    public function testGetItem(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        $this->col[] = new Item("Item 3");
        $this->col[] = new Item("Item 3");
        $this->assertNull($this->col->getItem(["var1" => "Item 4"]));
        $this->assertSame($this->col[0], $this->col->getItem(["var1" => "Item 1"]));
        $this->assertSame($this->col[2], $this->col->getItem(["var1" => "Item 3"]));
        $this->assertNotSame($this->col[3], $this->col->getItem(["var1" => "Item 3"]));
    }

    public function testRemoveByFilter(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        $this->col[] = new Item("Item 3");
        $this->col->removeByFilter(["var1!=" => "Item 1"]);
        $this->assertCount(1, $this->col);
        $this->col->lock();
        $this->assertThrowsException(function () {
            $this->col->removeByFilter(["var1" => "Item 1"]);
        }, \RuntimeException::class);
    }

    public function testGetIndex(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        $this->col[] = new Item("Item 3");
        $this->assertSame(0, $this->col->getIndex(["var1" => "Item 1"]));
        $this->assertNull($this->col->getIndex(["var1" => "Item"]));
    }
}
