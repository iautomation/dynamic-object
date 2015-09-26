# DynamicObject-for-PHP
Array and Object access at the same time, with a change callback

Implements ArrayAccess and Iterator, and uses PHP's magic methods in order to acheive a class instance accessible as an object and an array. Iterating is supported. Change callback supported.

### Usage
```
$object = new DynamicObject\DynamicObject([
	'one'=>1,
	'two'=>2
], function($class, $key_changed, $value_changed){
	echo "\n\n$key_changed is set to: ";
	print_r($value_changed);
});
$object->three = 3;
$object['four'] = 4;

echo $object['four'];

foreach($object as $key=>$value){
	echo ",$key";
}

$object->setChangeCallback(function($class, $key_changed, $value_changed){
	// your code here
});

print_r($object->toArray());
print_r($object->toObject());
```
