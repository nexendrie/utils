<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * MaxSizedCollection
 *
 * @author Jakub Konečný
 */
final class MaxSizedCollection extends Collection {
  /** @var string */
  protected $class = Item::class;
  /** @var int */
  protected $maxSize = 1;
}
?>