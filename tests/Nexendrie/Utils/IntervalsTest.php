<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

use Tester\Assert;

require __DIR__ . "/../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class IntervalsTest extends \Tester\TestCase
{
    private function tryInterval(string $input, ?string $result): void
    {
        Assert::same($result, Intervals::findInterval($input));
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
        Assert::true(Intervals::isInInterval(10, "{10}"));
        Assert::true(Intervals::isInInterval(-15, "{-15}"));
        Assert::false(Intervals::isInInterval(10, "abc"));
        Assert::false(Intervals::isInInterval(-10, "{-15}"));
        Assert::false(Intervals::isInInterval(10, "{1,2,3,9,15}"));
        Assert::true(Intervals::isInInterval(9, "{1,2,3,9,15}"));
        Assert::true(Intervals::isInInterval(-10, "{1,2,3,9,15,-10}"));
        Assert::false(Intervals::isInInterval(2, "[9,1]"));
        Assert::false(Intervals::isInInterval(1, "[2,5]"));
        Assert::false(Intervals::isInInterval(10, "[2,5]"));
        Assert::false(Intervals::isInInterval(2, "]2,5]"));
        Assert::false(Intervals::isInInterval(5, "[2,5["));
        Assert::true(Intervals::isInInterval(2, "[2,5]"));
        Assert::true(Intervals::isInInterval(5, "[2,5]"));
        Assert::true(Intervals::isInInterval(3, "[2,5]"));
        Assert::true(Intervals::isInInterval(-3, "[-5,-2]"));
        Assert::true(Intervals::isInInterval(0, "]-Inf,+Inf["));
        Assert::true(Intervals::isInInterval(-10, "]-Inf,+Inf["));
        Assert::true(Intervals::isInInterval(0, "]-Inf,1]"));
        Assert::true(Intervals::isInInterval(0, "[0,+Inf["));
        Assert::false(Intervals::isInInterval(0, "]+Inf,-Inf["));
    }
}

$test = new IntervalsTest();
$test->run();
