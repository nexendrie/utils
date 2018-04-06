<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * MaxSizedCollection
 *
 * @author Jakub Konečný
 */
final class MaxSizedCollection extends Collection {
  protected $class = Item::class;
  protected $maxSize = 1;
}
?>