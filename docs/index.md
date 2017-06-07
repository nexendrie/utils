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

```

Intervals
---------

There is class Intervals in this package which makes working with number intervals in PHP a piece of cake. It first method findInterval returns interval from input string or NULL if no valid interval was found. The second method isInInterval tells you whether an integer (first argument) is in specified interval (second parameter).

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

 
