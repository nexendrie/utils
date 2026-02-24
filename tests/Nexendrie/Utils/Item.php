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
    public function __construct(
        public string $var1,
        public BasicEnum $var2 = BasicEnum::ABC,
        public BackedEnum $var3 = BackedEnum::ABC
    ) {
    }

    public function method(string $value = "1"): bool
    {
        return ($this->var1 === $value);
    }
}
