<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

use MyTester\Attributes\TestSuite;

#[TestSuite("Constants")]
final class ConstantsTest extends \MyTester\TestCase
{
    public const string ABC_A = "a";
    public const string ABC_B = "b";
    public const string DEF_A = "d";

    public function testGetValues(): void
    {
        $result = Constants::getValues(self::class, "ABC_");
        $this->assertType("array", $result);
        $this->assertCount(2, $result);
        foreach ($result as $item) {
            $this->assertType("string", $item);
        }
        $this->assertSame("a", $result[0]);
        $this->assertSame("b", $result[1]);

        $result = Constants::getValues(self::class);
        $this->assertType("array", $result);
        if (version_compare(PHP_VERSION, "8.4.0") >= 0) {
            $this->assertCount(8, $result);
        } else {
            $this->assertCount(9, $result);
        }

        $result = Constants::getValues(
            class: self::class,
            visibilities: [\ReflectionClassConstant::IS_PUBLIC]
        );
        $this->assertType("array", $result);
        $this->assertCount(8, $result);

        $result = Constants::getValues(
            class: self::class,
            visibilities: [\ReflectionClassConstant::IS_PROTECTED]
        );
        $this->assertType("array", $result);
        if (version_compare(PHP_VERSION, "8.4.0") >= 0) {
            $this->assertCount(0, $result);
        } else {
            $this->assertCount(1, $result);
        }

        $this->assertThrowsException(static function () {
            Constants::getValues(class: self::class, visibilities: [15]);
        }, \DomainException::class);
    }
}
