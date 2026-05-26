# Basic Usage

The following examples demonstrate how to use the hydrators by using the [ArraySerializableHydrator](hydrators/array-serializable-hydrator.md).
PHP's `ArrayObject` is used as the object to hydrate and extract data from because it defines the `getArrayCopy()` method and the `exchangeArray()` method, which are used by the hydrator.

## Hydrating an Object

To hydrate an object with data, instantiate the hydrator and then pass to it the data for hydrating the object.

```php
$hydrator = new Laminas\Hydrator\ArraySerializableHydrator();

$data = [
    'first_name'    => 'James',
    'last_name'     => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number'  => '+61 419 1234 5678',
];

$person = $hydrator->hydrate($data, new ArrayObject());
```

The hydrated object will be an instance of `ArrayObject`.
The hydrator will use the keys of the input data to set the properties for the object.

By using `var_dump()` on the hydrated object, the following output will be produced:

```php
object(ArrayObject) {
  ["storage":"ArrayObject":private]=>
  array(4) {
    ["first_name"]=>
    string(5) "James"
    ["last_name"]=>
    string(4) "Kahn"
    ["email_address"]=>
    string(22) "james.kahn@example.org"
    ["phone_number"]=>
    string(17) "+61 419 1234 5678"
  }
}
```

## Extracting Values From an Object

To extract data from an object, instantiate the applicable hydrator and then call `extract`, passing to it the object to extract data from.

```php
$hydrator = new Laminas\Hydrator\ArraySerializableHydrator();

$person = new ArrayObject([
    'first_name'    => 'James',
    'last_name'     => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number'  => '+61 419 1234 5678',
]);

$data = $hydrator->extract($person);
```

The extracted data will be an array with the same keys as the input data.

By using `var_dump()` on the extracted data, the following output will be produced:

```php
array(4) {
  ["first_name"]=>
  string(5) "James"
  ["last_name"]=>
  string(4) "Kahn"
  ["email_address"]=>
  string(22) "james.kahn@example.org"
  ["phone_number"]=>
  string(17) "+61 419 1234 5678"
}
```

## More Hydrators

More useful hydrators are available to cover typical use cases:

- [ClassMethodsHydrator](hydrators/class-methods-hydrator.md) to hydrate objects using class methods
- [ObjectPropertyHydrator](hydrators/object-property-hydrator.md) to hydrate objects using object properties
- [ReflectionHydrator](hydrators/reflection-hydrator.md) to hydrate objects using reflection
