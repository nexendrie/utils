<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * Item
 *
 * @author Jakub Konečný
 */
class Item {
  /** @var string */
  public $var1;
  
  public function __construct(string $var1) {
    $this->var1 = $var1;
  }
}
?>