<?php
declare(strict_types=1);

namespace Nexendrie\Utils;

use Nette\Utils\Strings;
use ReflectionClassConstant as RCC;

/**
 * Constants
 *
 * @author Jakub Konečný
 */
final class Constants {
  use \Nette\StaticClass;
  
  /**
   * Get values of all constants from class $class whose name starts with $prefix
   *
   * @throws \ReflectionException
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
      if(Strings::startsWith($name, $prefix)) {
        $values[] = $value;
      }
    }
    return $values;
  }
}
?>