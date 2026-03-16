<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * @author Jakub Konečný
 * @testCase
 */
final class EnumsTest extends \MyTester\TestCase
{
    public function testGetValues(): void
    {
        $this->assertSame(Enums::getValues(BasicEnum::class), ["ABC", "DEF", "GHI",]);
        $this->assertSame(Enums::getValues(BasicEnum::class, "A"), ["ABC",]);
        $this->assertSame(Enums::getValues(BackedEnum::class), ["abc", "def", "ghi",]);
        $this->assertSame(Enums::getValues(BackedEnum::class, "A"), ["abc",]);
    }
}
