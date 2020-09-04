<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

/**
 * ParameterCollection
 *
 * @author Jakub Konečný
 */
final class ParameterCollection extends Collection {
  protected string $class = Item::class;
  public string $name = "";
  
  public function __construct(string $name) {
    parent::__construct();
    $this->name = $name;
  }
}
?>