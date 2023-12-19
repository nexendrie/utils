<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;

/**
 * @author Jakub Konečný
 * @testCase
 */
final class ConstantsTest extends \Tester\TestCase {
  public const ABC_A = "a";
  public const ABC_B = "b";
  public const DEF_A = "d";
  
  public function testGetConstantsValues() {
    $result = Constants::getConstantsValues(static::class, "ABC_");
    Assert::type("array", $result);
    Assert::count(2, $result);
    foreach($result as $item) {
      Assert::type("string", $item);
    }
    Assert::same("a", $result[0]);
    Assert::same("b", $result[1]);

    $result = Constants::getConstantsValues(static::class);
    Assert::type("array", $result);
    Assert::count(5, $result);

    $result = Constants::getConstantsValues(class: static::class, visibilities: [\ReflectionClassConstant::IS_PUBLIC]);
    Assert::type("array", $result);
    Assert::count(5, $result);

    $result = Constants::getConstantsValues(class: static::class, visibilities: [\ReflectionClassConstant::IS_PROTECTED]);
    Assert::type("array", $result);
    Assert::count(0, $result);

    Assert::exception(function () {
      Constants::getConstantsValues(class: static::class, visibilities: [15]);
    }, \DomainException::class);
  }
}

$test = new ConstantsTest();
$test->run();
?>