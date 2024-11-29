<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

require __DIR__ . "/../../bootstrap.php";

use Tester\Assert;

/**
 * @author Jakub Konečný
 * @testCase
 */
final class EnumsTest extends \Tester\TestCase {
  public function testGetValues(): void {
    Assert::same(Enums::getValues(BasicEnum::class), ["ABC", "DEF", "GHI", ]);
    Assert::same(Enums::getValues(BasicEnum::class, "A"), ["ABC", ]);
    Assert::same(Enums::getValues(BackedEnum::class), ["abc", "def", "ghi", ]);
    Assert::same(Enums::getValues(BackedEnum::class, "A"), ["abc", ]);
  }
}

$test = new EnumsTest();
$test->run();
?>