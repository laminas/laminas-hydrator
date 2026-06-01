# ArraySerializableHydrator

The ArraySerializableHydrator hydrates data from an array and extracts an object’s data returning it as an array.
Objects passed to the hydrate method must implement either `exchangeArray()` or `populate()` to support hydration, and must implement `getArrayCopy()` to support extraction.

## Example

### Hydration

```php
$hydrator = new Laminas\Hydrator\ArraySerializableHydrator();

$data = [
    'first_name'    => 'James',
    'last_name'     => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number'  => '+61 419 1234 5678',
];

$object = $hydrator->hydrate($data, new ArrayObject());
```

### Extraction

```php
$hydrator = new Laminas\Hydrator\ArraySerializableHydrator();

// ... Assuming that $object has already been initialised
$data = $hydrator->extract($object);
```
