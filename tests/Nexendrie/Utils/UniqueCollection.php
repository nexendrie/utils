<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * UniqueCollection
 *
 * @author Jakub Konečný
 */
class UniqueCollection extends Collection {
  protected $class = Item::class;
  protected $uniqueProperty = "var1";
}
?>