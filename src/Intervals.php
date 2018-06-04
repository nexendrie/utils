<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

use Nette\Utils\Strings;

/**
 * Intervals
 *
 * @author Jakub Konečný
 */
class Intervals {
  use \Nette\StaticClass;
  
  public const PATTERN = '/(\{\-?\d+(,\-?\d+)*\})|((?P<start>\[|\])(?P<limit1>\-?\d+|\-Inf),(?P<limit2>\-?\d+|\+Inf)(?P<end>\[|\]))/';
  
  public static function findInterval(string $text): ?string {
    $found = preg_match(static::PATTERN, $text, $result);
    if(!$found) {
      return null;
    }
    return $result[0];
  }
  
  public static function isInInterval(int $number, string $interval): bool {
    if(is_null(static::findInterval($interval)) OR $interval !== static::findInterval($interval)) {
      return false;
    }
    if(Strings::startsWith($interval, "{")) {
      $numbers = explode(",", Strings::trim($interval, "{}"));
      array_walk($numbers, function(&$value) {
        $value = (int) $value;
      });
      return (in_array($number, $numbers, true));
    }
    preg_match(static::PATTERN, $interval, $matches);
    $start = $matches["start"][0];
    $end = $matches["end"][0];
    $limit1 = (int) str_replace("-Inf", PHP_INT_MIN, $matches["limit1"]);
    $limit2 = (int) str_replace("+Inf", PHP_INT_MAX, $matches["limit2"]);
    if($limit1 > $limit2) {
      return false;
    } elseif($number < $limit1) {
      return false;
    } elseif($number > $limit2) {
      return false;
    } elseif($number === $limit1 AND $start === "]") {
      return false;
    } elseif($number === $limit2 AND $end === "[") {
      return false;
    }
    return true;
  }
}
?>