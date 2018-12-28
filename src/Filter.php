<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

use Nette\Utils\Strings;

/**
 * Filter
 *
 * @author Jakub Konečný
 * @internal
 */
final class Filter {
  use \Nette\StaticClass;
  
  public const OPERATORS = ["==", ">=", ">", "<=", "<", "!=",];
  
  public static function getOperator(string $input): string {
    foreach(static::OPERATORS as $operator) {
      if(Strings::endsWith($input, $operator)) {
        return $operator;
      }
    }
    return static::OPERATORS[0];
  }
  
  public static function applyFilter(array $input, array $filter = []): array {
    if(count($filter) === 0) {
      return $input;
    }
    return array_values(array_filter($input, function(object $item) use($filter) {
      /** @var string $key */
      foreach($filter as $key => $value) {
        $operator = static::getOperator($key);
        $key = Strings::endsWith($key, $operator) ? Strings::before($key, $operator) : $key;
        if(!eval("return \"{$item->$key}\" $operator \"$value\";")) {
          return false;
        }
      }
      return true;
    }));
  }
}
?>