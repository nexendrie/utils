<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * Collection
 *
 * @author Jakub Konečný
 */
abstract class Collection implements \ArrayAccess, \Countable, \IteratorAggregate {
  use TCollection;
}
?>