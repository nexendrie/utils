<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

use Nette\Utils\Strings;

/**
 * Intervals
 *
 * @author Jakub Konečný
 */
final class Intervals {
  public const PATTERN = '/(\{\-?\d+(,\-?\d+)*\})|((?P<start>\[|\])(?P<limit1>\-?\d+|\-Inf),(?P<limit2>\-?\d+|\+Inf)(?P<end>\[|\]))/';

  private function __construct() {
  }

  public static function findInterval(string $text): ?string {
    preg_match(static::PATTERN, $text, $result);
    if(count($result) < 1) {
      return null;
    }
    /** @var string[] $result */
    return $result[0];
  }
  
  public static function isInInterval(int $number, string $interval): bool {
    if(static::findInterval($interval) === null || $interval !== static::findInterval($interval)) {
      return false;
    }
    if(str_starts_with($interval, "{")) {
      $numbers = explode(",", Strings::trim($interval, "{}"));
      array_walk($numbers, function(&$value): void {
        $value = (int) $value;
      });
      return (in_array($number, $numbers, true));
    }
    preg_match(static::PATTERN, $interval, $matches);
    /** @var array{start: string[], end: string[], limit1: string, limit2: string} $matches */
    $start = $matches["start"][0];
    $end = $matches["end"][0];
    $limit1 = (int) str_replace("-Inf", (string) PHP_INT_MIN, $matches["limit1"]);
    $limit2 = (int) str_replace("+Inf", (string) PHP_INT_MAX, $matches["limit2"]);
    if($limit1 > $limit2) {
      return false;
    } elseif($number < $limit1) {
      return false;
    } elseif($number > $limit2) {
      return false;
    } elseif($number === $limit1 && $start === "]") {
      return false;
    } elseif($number === $limit2 && $end === "[") {
      return false;
    }
    return true;
  }
}
?>