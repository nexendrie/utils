<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * UniqueCollection
 *
 * @author Jakub Konečný
 */
final class UniqueCollection extends Collection {
  /** @var string */
  protected $class = Item::class;
  /** @var string */
  protected $uniqueProperty = "var1";
}
?>