# DynamicObject-for-PHP
Array and Object access at the same time, with save and change callbacks

Implements ArrayAccess and Iterator, and uses PHP's magic methods in order to acheive a class instance accessible as an object and an array. Iterating is supported.

### Methods

```
public function __construct($data=[], $save_callback=null, $change_callback=null)
public function &__get ($key) 
public function __set($key, $value) 
public function __isset ($key) 
public function __unset($key) 
public function offsetSet($offset,$value) 
public function offsetExists($offset) 
public function offsetUnset($offset) 
public function offsetGet($offset) 
public function __call($method, $args) 
public function __invoke($args=[])
public function rewind() 
public function current() 
public function key() 
public function next() 
public function valid() 
public function setChangeCallback($change_callback=null) 
public function setSaveCallback($save_callback=null) 
public function save()
public function dataArray() 
public function dataObject() 
public function changesArray() 
public function changesObject() 
```

### Basic Usage
```
include 'DynamicObject/DynamicObject.php';

$data = [
	'one'=>1
];
$save_callback = function($class, $changes){
	echo "\n".'Saved:'."\n";
	print_r($changes);
};
$change_callback = function($class, $key, $value){
	echo "\n".'Changed:'."\n".$key.':'.$value;
};
$object = new \DynamicObject\DynamicObject($data, $save_callback, $change_callback);
$object->two = 2;
$object['three'] = 3;
$object->save();

print_r($object->dataArray());
print_r($object->changesArray());
```
