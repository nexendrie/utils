<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

use ReflectionClassConstant as RCC;

/**
 * Constants
 *
 * @author Jakub Konečný
 */
final class Constants {
  private function __construct() {
  }
  
  /**
   * Get values of all constants from class $class whose name starts with $prefix
   *
   * @param class-string $class
   * @param int[] $visibilities
   * @throws \ReflectionException
   * @throws \DomainException
   */
  public static function getConstantsValues(string $class, string $prefix = "", array $visibilities = [RCC::IS_PUBLIC, RCC::IS_PROTECTED, RCC::IS_PRIVATE,]): array {
    $allowedVisibilities = [
      RCC::IS_PUBLIC, RCC::IS_PROTECTED, RCC::IS_PRIVATE,
    ];
    foreach ($visibilities as $visibility) {
      if (!in_array($visibility, $allowedVisibilities, true)) {
        throw new \DomainException("Invalid visibility $visibility");
      }
    }
    $values = [];
    $constants = (new \ReflectionClass($class))->getConstants(array_sum($visibilities));
    foreach($constants as $name => $value) {
      if(str_starts_with($name, $prefix)) {
        $values[] = $value;
      }
    }
    return $values;
  }
}
?>