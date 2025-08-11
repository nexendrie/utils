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
  public const OPERATORS = ["==", ">=", ">", "<=", "<", "!=",];

  private function __construct() {
  }

  public static function getOperator(string $input): string {
    foreach(static::OPERATORS as $operator) {
      if(str_ends_with($input, $operator)) {
        return $operator;
      }
    }
    return static::OPERATORS[0];
  }

  /**
   * @param mixed $value
   */
  protected static function getCondition(object $item, string $key, string $operator, $value): string {
    if($key === "%class%") {
      return "return \"" . get_class($item) . "\" $operator \"$value\";";
    }
    if(preg_match("#([a-zA-Z0-9_]+)\(\)\$#", $key, $matches) === 1) {
      return "return  \"{$item->{$matches[1]}()}\" $operator  \"$value\";";
    }
    return "return \"{$item->$key}\" $operator \"$value\";";
  }

  public static function matches(object $item, array $filter): bool {
    /** @var string $key */
    foreach($filter as $key => $value) {
      $operator = static::getOperator($key);
      /** @var string $key */
      $key = str_ends_with($key, $operator) ? Strings::before($key, $operator) : $key;
      if(!eval(static::getCondition($item, $key, $operator, $value))) {
        return false;
      }
    }
    return true;
  }

  public static function applyFilter(array $input, array $filter = []): array {
    if(count($filter) === 0) {
      return $input;
    }
    return array_values(array_filter($input, function(object $item) use($filter): bool {
      return static::matches($item, $filter);
    }));
  }
}
?>