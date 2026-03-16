<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * @author Jakub Konečný
 * @testCase
 */
final class IntervalsTest extends \MyTester\TestCase
{
    private function tryInterval(string $input, ?string $result): void
    {
        $this->assertSame($result, Intervals::findInterval($input));
    }

    public function testFindInterval(): void
    {
        $this->tryInterval("abc", null);
        $this->tryInterval("abc{0}abc", "{0}");
        $this->tryInterval("abc{-15}abc", "{-15}");
        $this->tryInterval("abc{0,11,2}abc", "{0,11,2}");
        $this->tryInterval("abc{0,11,2,-15}abc", "{0,11,2,-15}");
        $this->tryInterval("abc[0]abc", null);
        $this->tryInterval("abc[0,1,0]abc", null);
        $this->tryInterval("abc[0,1]abc", "[0,1]");
        $this->tryInterval("abc]0,1]abc", "]0,1]");
        $this->tryInterval("abc[0,1[abc", "[0,1[");
        $this->tryInterval("abc]0,1[abc", "]0,1[");
        $this->tryInterval("abc]-15,-10[abc", "]-15,-10[");
        $this->tryInterval("abc]-Inf,+Inf[abc", "]-Inf,+Inf[");
        $this->tryInterval("]+Inf,-Inf[", null);
    }

    public function testIsInInterval(): void
    {
        $this->assertTrue(Intervals::isInInterval(10, "{10}"));
        $this->assertTrue(Intervals::isInInterval(-15, "{-15}"));
        $this->assertFalse(Intervals::isInInterval(10, "abc"));
        $this->assertFalse(Intervals::isInInterval(-10, "{-15}"));
        $this->assertFalse(Intervals::isInInterval(10, "{1,2,3,9,15}"));
        $this->assertTrue(Intervals::isInInterval(9, "{1,2,3,9,15}"));
        $this->assertTrue(Intervals::isInInterval(-10, "{1,2,3,9,15,-10}"));
        $this->assertFalse(Intervals::isInInterval(2, "[9,1]"));
        $this->assertFalse(Intervals::isInInterval(1, "[2,5]"));
        $this->assertFalse(Intervals::isInInterval(10, "[2,5]"));
        $this->assertFalse(Intervals::isInInterval(2, "]2,5]"));
        $this->assertFalse(Intervals::isInInterval(5, "[2,5["));
        $this->assertTrue(Intervals::isInInterval(2, "[2,5]"));
        $this->assertTrue(Intervals::isInInterval(5, "[2,5]"));
        $this->assertTrue(Intervals::isInInterval(3, "[2,5]"));
        $this->assertTrue(Intervals::isInInterval(-3, "[-5,-2]"));
        $this->assertTrue(Intervals::isInInterval(0, "]-Inf,+Inf["));
        $this->assertTrue(Intervals::isInInterval(-10, "]-Inf,+Inf["));
        $this->assertTrue(Intervals::isInInterval(0, "]-Inf,1]"));
        $this->assertTrue(Intervals::isInInterval(0, "[0,+Inf["));
        $this->assertFalse(Intervals::isInInterval(0, "]+Inf,-Inf["));
    }
}
