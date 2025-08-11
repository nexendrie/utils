<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

final class Enums {
  private function __construct() {
  }

  /**
   * Get values of all cases from enum $class whose name starts with $prefix
   *
   * @param class-string<\UnitEnum> $class
   * @throws \ReflectionException
   */
  public static function getValues(string $class, string $prefix = ""): array {
    $re = new \ReflectionEnum($class);
    $values = [];
    foreach ($re->getCases() as $case) {
      if (!str_starts_with($case->getName(), $prefix)) {
        continue;
      }
      if ($case instanceof \ReflectionEnumBackedCase) {
        $values[] = $case->getBackingValue();
      } else {
        $values[] = $case->getName();
      }
    }
    return $values;
  }
}
?>