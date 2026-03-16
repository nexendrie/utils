<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * @author Jakub Konečný
 * @testCase
 */
final class FilterTest extends \MyTester\TestCase
{
    public function testGetOperator(): void
    {
        $input = "abc";
        $this->assertSame("==", Filter::getOperator($input));
        foreach (Filter::OPERATORS as $operator) {
            $this->assertSame($operator, Filter::getOperator($input . $operator));
        }
    }

    public function testMatches(): void
    {
        $items = [
            new Item("1"), new Item("2", BasicEnum::DEF, BackedEnum::DEF),
        ];
        $this->assertTrue(Filter::matches($items[0], ["var1<=" => 1]));
        $this->assertFalse(Filter::matches($items[1], ["var1<=" => 1]));
        $this->assertTrue(Filter::matches($items[0], ["var2" => BasicEnum::ABC]));
        $this->assertFalse(Filter::matches($items[1], ["var2" => BasicEnum::ABC]));
        $this->assertTrue(Filter::matches($items[0], ["var3" => BackedEnum::ABC]));
        $this->assertFalse(Filter::matches($items[1], ["var3" => BackedEnum::ABC]));
        $this->assertTrue(Filter::matches($items[0], ["%class%" => Item::class]));
        $this->assertFalse(Filter::matches($items[0], ["%class%!=" => Item::class]));
        $this->assertTrue(Filter::matches($items[0], ["method()" => true]));
        $this->assertFalse(Filter::matches($items[0], ["method()!=" => true]));
    }

    public function testApplyFilter(): void
    {
        $items = [
            new Item("1"), new Item("2"), new Item("3", BasicEnum::DEF, BackedEnum::DEF),
        ];
        $this->assertCount(1, Filter::applyFilter($items, ["var1" => 1]));
        $this->assertCount(2, Filter::applyFilter($items, ["var2" => BasicEnum::ABC]));
        $this->assertCount(2, Filter::applyFilter($items, ["var3" => BackedEnum::ABC]));
        $this->assertCount(1, Filter::applyFilter($items, ["var1==" => 1]));
        $this->assertCount(2, Filter::applyFilter($items, ["var2==" => BasicEnum::ABC]));
        $this->assertCount(2, Filter::applyFilter($items, ["var3==" => BackedEnum::ABC]));
        $this->assertCount(3, Filter::applyFilter($items, ["var1>=" => 1]));
        $this->assertCount(2, Filter::applyFilter($items, ["var1>" => 1]));
        $this->assertCount(2, Filter::applyFilter($items, ["var1<=" => 2]));
        $this->assertCount(1, Filter::applyFilter($items, ["var1<" => 2]));
        $this->assertCount(2, Filter::applyFilter($items, ["var1!=" => 3]));
        $this->assertCount(1, Filter::applyFilter($items, ["var2!=" => BasicEnum::ABC]));
        $this->assertCount(1, Filter::applyFilter($items, ["var3!=" => BackedEnum::ABC]));
    }
}
