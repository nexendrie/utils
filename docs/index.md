Utils
==============

This package contains various utilities for you PHP project.

Links
-----

Primary repository: https://gitlab.com/nexendrie/utils
Github repository: https://github.com/nexendrie/utils
Packagist: https://packagist.org/packages/nexendrie/utils

Installation
------------
The best way to install it is via Composer. Just add **nexendrie/utils** to your dependencies.

Collection
----------

This package contains abstract class Nexendrie\Utils\Collection which is a base for collection of objects of specified type. The type is stored in property $class. The collection behaves like an array - you can use count() on it, you can iterate over the items in foreach, you can even add new items as next element of array. Example of usage:

```php
<?php
declare(strict_types=1);

class Item {
  
}

class MyCollection extends Nexendrie\Utils\Collection {
  protected $class = Item::class;
}

$collection = new MyCollection();
$collection[] = new Item();
$collection[] = new Item();
count($collection); // 2
foreach($collection as $index => $item) {
  if($index === 1) {
    unset($collection[$index]);
  }
}
count($collection); // 1
?>
```

You can even create collection in which all items have to have different values for 1 property. Just set (or overwrite) value for property $uniqueProperty.

It is also possible to lock collection to prevent adding and deleting items. Just use method lock on the collection.

If you need to set a limit of number of items in the collection, just set value for property $maxSize:

```php
<?php
declare(strict_types=1);

class Item {
  
}

class MyCollection extends Nexendrie\Utils\Collection {
  protected $class = Item::class;
  protected $maxSize = 5;
}
?>
```

If you cannot (or do not want to) extend that class, you can also use trait Nexendrie\Utils\TCollection in your class. Do not forget to make the class implement \ArrayAccess, \Countable and \IteratorAggregate interfaces.

### Custom checks

You can add additional validation rules for items in form of callbacks. Just add new array item to property $checks or use method addChecker. The callback gets new item as first parameter and the collection as second, if it does not throw any exception, item is added to the collection.

### Filtering

It is possible to find out if the collection has at least 1 item meeting specific criteria/get array containing that items via methods hasItems/getItems. Both of them accept array as argument in form property => value; if it contains multiple conditions, all of them have to be met. The property name can be followed by an operator from the following list: ==, >=, >, <=, <, !=, default is ==. You can use %class% as property if you want to filter on item's type.

If you want to get only the first item matching the filter, use method getItem. If there is no such item, null is returned.

You can also remove all items matching the filter with method removeByFilter.

Intervals
---------

There is class Intervals in this package which makes working with number intervals in PHP a piece of cake. Its first method findInterval returns interval from input string or NULL if no valid interval was found. The second method isInInterval tells you whether an integer (first argument) is in specified interval (second parameter).

### Supported formats for interval

```
{1} number 1
{2,5,7,-10} numbers 2, 5, 7 and -10
[-1,3] numbers -1, 0, 1, 2 and 3
]1,3] numbers 2 and 3
[1,3[ numbers 1 and 2
]-Inf,1] any number bellow 2
[1,+Inf[ any number above 0
[-Inf,+Inf[ any number
```

Class constants
---------------

If you need to get values of constants from certain class (e. g. for validating values), you can use method Nexendrie\Utils\Constants::getConstantsValues. It takes class name as first parameter, in second parameter you can specify string which all constant names should start with. Examples:

```php
<?php
declare(strict_types=1);

use Nexendrie\Utils\Constants;

class Abcd {
  public const ABC = "abc";
  protected const DEF = "def";
  private const AMV = "amv";
}

count(Constants::getConstantsValues(Abcd::class)); //3
count(Constants::getConstantsValues(Abcd::class, "A")); //2
?>
```

Numbers
-------

Class Nexendrie\Utils\Numbers has method range that can be used to ensure that a number is between certain values. It takes the number, minimum and maximum as parameters. Examples:

```php
<?php
declare(strict_types=1);

use Nexendrie\Utils\Numbers;

Numbers::range(-10, 0, 50); //0
Numbers::range(100, 0, 50); //50
Numbers::range(25, 0, 50); //25
?>
```

If you just want to know whether a number is between certain values, use method isInRange(). Examples:

```php
<?php
declare(strict_types=1);

use Nexendrie\Utils\Numbers;

Numbers::isInRange(0, 0, 5); //true
Numbers::isInRange(3, 0, 5); //true
Numbers::isInRange(5, 0, 5); //true
Numbers::isInRange(-1, 0, 5); //false
Numbers::isInRange(6, 0, 5); //false
?>
```
