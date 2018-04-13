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
    $filter = ["var1<=" => 2];
    $result = Filter::applyFilter($items, $filter);
    Assert::type("array", $result);
    Assert::count(2, $result);
  }
}

$test = new FilterTest();
$test->run();
?>