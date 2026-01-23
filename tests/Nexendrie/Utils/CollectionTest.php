<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class CollectionTest extends \Tester\TestCase
{
    protected Collection $col;

    public function setUp(): void
    {
        $this->col = new TestCollection();
    }

    public function testCount(): void
    {
        Assert::same(0, count($this->col));
        $this->col[] = new Item("Item 1");
        Assert::count(1, $this->col);
    }

    public function testGetIterator(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->col[] = new Item("Item 1");
        }
        /** @var Item $item */
        foreach ($this->col as $item) {
            Assert::same("Item 1", $item->var1);
        }
    }

    public function testOffsetExists(): void
    {
        Assert::false(isset($this->col[0]));
        $this->col[] = new Item("Item 1");
        Assert::true(isset($this->col[0]));
    }

    public function testOffsetGet(): void
    {
        $this->col[] = new Item("Item 1");
        $item = $this->col[0];
        Assert::type(Item::class, $item);
        Assert::exception(function () {
            $item = $this->col[1];
        }, \OutOfRangeException::class);
    }

    public function testOffsetSet(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[0] = new Item("Item 2");
        Assert::same("Item 2", $this->col[0]->var1);
        Assert::exception(function () {
            $this->col[] = new \stdClass();
        }, \InvalidArgumentException::class);
        Assert::exception(function () {
            $this->col[-1] = new Item("Item 1");
        }, \OutOfRangeException::class);
    }

    public function testUniqueness(): void
    {
        $col = new UniqueCollection();
        $col[] = new Item("Item 1");
        Assert::exception(static function () use ($col) {
            $col[] = new Item("Item 1");
        }, \RuntimeException::class, "Duplicate var1 Item 1.");
    }

    public function testOffsetUnset(): void
    {
        $this->col[] = new Item("Item 1");
        unset($this->col[0]);
        Assert::false(isset($this->col[0]));
        Assert::exception(function () {
            unset($this->col[0]);
        }, \OutOfRangeException::class);
    }

    public function testLocking(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col->lock();
        Assert::true($this->col->isLocked());
        Assert::exception(function () {
            $this->col[] = new Item("Item 1");
        }, \RuntimeException::class);
        Assert::exception(function () {
            unset($this->col[0]);
        }, \RuntimeException::class);
    }

    public function testMaxSize(): void
    {
        $col = new MaxSizedCollection();
        $col[] = new Item("Item 1");
        Assert::exception(static function () use ($col) {
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
        Assert::exception(static function () use ($col) {
            $col[] = new Item("Item 2");
        }, \RuntimeException::class, "");
    }

    public function testToArray(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        $array = $this->col->toArray();
        Assert::type("array", $array);
        Assert::count(2, $array);
    }

    public function testFromArray(): void
    {
        $items = [
            new Item("Item 1"), new Item("Item 2"),
        ];
        $collection = TestCollection::fromArray($items);
        Assert::type(TestCollection::class, $collection);
        Assert::count(2, $collection);
        $collection = ParameterCollection::fromArray($items, "abc");
        Assert::type(ParameterCollection::class, $collection);
        Assert::count(2, $collection);
        Assert::same("abc", $collection->name);
    }

    public function testHasItems(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        Assert::true($this->col->hasItems());
        Assert::true($this->col->hasItems(["var1" => "Item 1"]));
        Assert::false($this->col->hasItems(["var1" => "Item 3"]));
        Assert::false($this->col->hasItems(["var1" => "Item 1"], 2));
    }

    public function testGetItems(): void
    {
        Assert::count(0, $this->col->getItems());
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        $this->col[] = new Item("Item 3");
        Assert::count(3, $this->col->getItems());
        Assert::count(1, $this->col->getItems(["var1" => "Item 1"]));
        Assert::count(0, $this->col->getItems(["var1" => "Item 4"]));
        Assert::count(2, $this->col->getItems(["var1<" => "Item 3"]));
    }

    public function testGetItem(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        $this->col[] = new Item("Item 3");
        $this->col[] = new Item("Item 3");
        Assert::null($this->col->getItem(["var1" => "Item 4"]));
        Assert::same($this->col[0], $this->col->getItem(["var1" => "Item 1"]));
        Assert::same($this->col[2], $this->col->getItem(["var1" => "Item 3"]));
        Assert::notSame($this->col[3], $this->col->getItem(["var1" => "Item 3"]));
    }

    public function testRemoveByFilter(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        $this->col[] = new Item("Item 3");
        $this->col->removeByFilter(["var1!=" => "Item 1"]);
        Assert::count(1, $this->col);
        $this->col->lock();
        Assert::exception(function () {
            $this->col->removeByFilter(["var1" => "Item 1"]);
        }, \RuntimeException::class);
    }

    public function testGetIndex(): void
    {
        $this->col[] = new Item("Item 1");
        $this->col[] = new Item("Item 2");
        $this->col[] = new Item("Item 3");
        Assert::same(0, $this->col->getIndex(["var1" => "Item 1"]));
        Assert::null($this->col->getIndex(["var1" => "Item"]));
    }
}

$test = new CollectionTest();
$test->run();
