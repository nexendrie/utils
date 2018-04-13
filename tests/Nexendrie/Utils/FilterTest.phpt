<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;

final class FilterTest extends \Tester\TestCase {
  public function testGetOperator() {
    $input = "abc";
    Assert::same("==", Filter::getOperator($input));
    foreach(Filter::OPERATORS as $operator) {
      Assert::same($operator, Filter::getOperator($input . $operator));
    }
  }
  
  public function testApplyFilter() {
    $items = [
      new Item("1"), new Item("2"), new Item("3"),
    ];
    Assert::count(1, Filter::applyFilter($items, ["var1" => 1]));
    Assert::count(1, Filter::applyFilter($items, ["var1==" => 1]));
    Assert::count(3, Filter::applyFilter($items, ["var1>=" => 1]));
    Assert::count(2, Filter::applyFilter($items, ["var1>" => 1]));
    Assert::count(2, Filter::applyFilter($items, ["var1<=" => 2]));
    Assert::count(1, Filter::applyFilter($items, ["var1<" => 2]));
    Assert::count(2, Filter::applyFilter($items, ["var1!=" => 3]));
  }
}

$test = new FilterTest();
$test->run();
?>