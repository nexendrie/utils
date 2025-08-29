<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * UniqueCollection
 *
 * @author Jakub Konečný
 */
final class UniqueCollection extends Collection
{
    protected string $class = Item::class;
    protected ?string $uniqueProperty = "var1";
}
