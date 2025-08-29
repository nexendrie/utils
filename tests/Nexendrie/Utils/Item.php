<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * Item
 *
 * @author Jakub Konečný
 */
final class Item
{
    public string $var1;

    public function __construct(string $var1)
    {
        $this->var1 = $var1;
    }

    public function method(string $value = "1"): bool
    {
        return ($this->var1 === $value);
    }
}
