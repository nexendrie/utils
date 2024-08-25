<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;

final class NumbersTest extends \Tester\TestCase {
  public function testRange(): void {
    Assert::same(0, Numbers::range(-10, 0, 50));
    Assert::same(50, Numbers::range(100, 0, 50));
    Assert::same(25, Numbers::range(25, 0, 50));
  }
  
  public function testIsInRange(): void {
    Assert::true(Numbers::isInRange(0, 0, 5));
    Assert::true(Numbers::isInRange(3, 0, 5));
    Assert::true(Numbers::isInRange(5, 0, 5));
    Assert::false(Numbers::isInRange(-1, 0, 5));
    Assert::false(Numbers::isInRange(6, 0, 5));
  }
}

$test = new NumbersTest();
$test->run();
?>