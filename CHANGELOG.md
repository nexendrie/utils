Version 1.9.0-dev
- raised minimal version of PHP to 8.1
- possible BC break: updated signature of TCollection::offsetGet() for PHP 8.1
- added class Enums

Version 1.8.1
- fixed compatibility with PHP 8.1

Version 1.8.0
- raised minimal version of PHP to 7.4
- used typed properties (possible BC break)

Version 1.7.0
- dropped support for Nette 2.4
- raised minimal version of PHP to 7.3

Version 1.6.1
- allowed Nette 3

Version 1.6.0
- added optional parameter $count to (T)Collection::hasItems()
- marked classes Constants, Intervals and Numbers as final (possible BC break)
- allowed to filter by return value of item's method in collections

Version 1.5.1
- fixed (T)Collection::removeByFilter() failing when multiple items match the filter

Version 1.5.0
- added methods getItem, removeByFilter, getIndex to (T)Collection
- raised minimal version of PHP to 7.2
- allowed to filter by class in collections

Version 1.4.0
- added methods hasItems/getItems and fromArray to (T)Collection
- added method Numbers::isInRange()

Version 1.3.0
- collections can have max size
- added support for custom checkers to collection
- reimplemented default checks in TCollection as checkers (possible BC break)
- added method (T)Collection::toArray()

Version 1.2.0
- some refactoring/code cleaning
- added classes Constants and Numbers
- added option to lock collection

Version 1.1.0
- added trait-based version of Collection
- added support for unique collections

Version 1.0.0
- first version
