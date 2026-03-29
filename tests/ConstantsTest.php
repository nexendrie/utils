<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * @author Jakub Konečný
 * @testCase
 */
final class ConstantsTest extends \MyTester\TestCase
{
    public const string ABC_A = "a";
    public const string ABC_B = "b";
    public const string DEF_A = "d";

    public function testGetConstantsValues(): void
    {
        $result = Constants::getConstantsValues(self::class, "ABC_"); // @phpstan-ignore staticMethod.deprecated
        $this->assertType("array", $result);
        $this->assertCount(2, $result);
        foreach ($result as $item) {
            $this->assertType("string", $item);
        }
        $this->assertSame("a", $result[0]);
        $this->assertSame("b", $result[1]);

        $result = Constants::getConstantsValues(self::class); // @phpstan-ignore staticMethod.deprecated
        $this->assertType("array", $result);
        $this->assertCount(9, $result);

        $result = Constants::getConstantsValues( // @phpstan-ignore staticMethod.deprecated
            class: self::class,
            visibilities: [\ReflectionClassConstant::IS_PUBLIC]
        );
        $this->assertType("array", $result);
        $this->assertCount(8, $result);

        $result = Constants::getConstantsValues( // @phpstan-ignore staticMethod.deprecated
            class: self::class,
            visibilities: [\ReflectionClassConstant::IS_PROTECTED]
        );
        $this->assertType("array", $result);
        $this->assertCount(1, $result);

        $this->assertThrowsException(static function () {
            // @phpstan-ignore staticMethod.deprecated
            Constants::getConstantsValues(class: self::class, visibilities: [15]);
        }, \DomainException::class);
    }

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
        $this->assertCount(9, $result);

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
        $this->assertCount(1, $result);

        $this->assertThrowsException(static function () {
            Constants::getValues(class: self::class, visibilities: [15]);
        }, \DomainException::class);
    }
}
