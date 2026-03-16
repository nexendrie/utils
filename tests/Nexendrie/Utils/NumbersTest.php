<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

final class NumbersTest extends \MyTester\TestCase
{
    public function testRange(): void
    {
        $this->assertSame(0, Numbers::range(-10, 0, 50)); // @phpstan-ignore staticMethod.deprecated
        $this->assertSame(50, Numbers::range(100, 0, 50)); // @phpstan-ignore staticMethod.deprecated
        $this->assertSame(25, Numbers::range(25, 0, 50)); // @phpstan-ignore staticMethod.deprecated
    }

    public function testClam(): void
    {
        $this->assertSame(0, Numbers::clamp(-10, 0, 50));
        $this->assertSame(50, Numbers::clamp(100, 0, 50));
        $this->assertSame(25, Numbers::clamp(25, 0, 50));
    }

    public function testIsInRange(): void
    {
        $this->assertTrue(Numbers::isInRange(0, 0, 5));
        $this->assertTrue(Numbers::isInRange(3, 0, 5));
        $this->assertTrue(Numbers::isInRange(5, 0, 5));
        $this->assertFalse(Numbers::isInRange(-1, 0, 5));
        $this->assertFalse(Numbers::isInRange(6, 0, 5));
    }
}
