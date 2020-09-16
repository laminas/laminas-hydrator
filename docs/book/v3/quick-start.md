# laminas-hydrator

The laminas-hydrator component provides functionality for hydrating objects (which is the act of populating an object from a set of data) and extracting data from them.

The component contains [concrete implementations](#available_implementations) for a number of common use cases, such as by using arrays, object methods, and reflection, and provides [interfaces](#base_interfaces) for creating custom implementations.

## Usage

### Hydrating an Object

To hydrate an object with data, instantiate the hydrator and then pass to it the data for hydrating the object.

```php
<?php

use Laminas\Hydrator;

$hydrator = new Hydrator\ArraySerializableHydrator();

$data = [
    'first_name' => 'James',
    'last_name' => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number' => '+61 419 1234 5678',
];

$object = $hydrator->hydrate($data, new ArrayObject());
```

### Extracting Values From an Object

To extract data from an object, instantiate the applicable hydrator and then call `extract`, passing to it the object to extract data from.

```php
<?php

use Laminas\Hydrator;

$hydrator = new Hydrator\ArraySerializableHydrator();

$data = [
    'first_name' => 'James',
    'last_name' => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number' => '+61 419 1234 5678',
];

$data = $hydrator->extract($object);
```

<a name="available_implementations"></a>
## Available Implementations

### ArraySerializableHydrator

The ArraySerializableHydrator hydrates data from an array and extracts an object’s data returning it as an array.
Objects passed to the hydrate method must implement either `exchangeArray()` or `populate()` to support hydration, and must implement `getArrayCopy()` to support extraction.

### ClassMethodsHydrator

The ClassMethodsHydrator calls "setter" methods matching keys in the data set to hydrate objects and calls "getter" methods matching keys in the data set during extraction, based on the following rules:

- `is*()`, `has*()`, and `get*()` methods will be used when extracting data.
  The method prefix will be removed from the key's name.
- `set*()` methods will be used when hydrating properties.

```php
<?php

use Laminas\Hydrator;

class User
{
    private string $firstName;
    private string $lastName;
    private string $emailAddress;
    private string $phoneNumber;

    public function setFirstName(string $firstName) {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName) {
        $this->lastName = $lastName;
    }

    public function setEmailAddress(string $emailAddress) {
        $this->emailAddress = $emailAddress;
    }

    public function setPhoneNumber(string $phoneNumber) {
        $this->phoneNumber = $phoneNumber;
    }
}

$data = [
    'first_name' => 'James',
    'last_name' => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number' => '+61 419 1234 5678',
];

$hydrator = new Hydrator\ClassMethodsHydrator();
$user = $delegating->hydrate($data, new User());
$data = $delegating->extract(new User());
```

### ObjectPropertyHydrator

The ObjectPropertyHydrator hydrates objects and extracts data using publicly accessible properties which match a key in the data set.

```php
<?php

use Laminas\Hydrator;

class User
{
    public string $firstName;
    public string $lastName;
    public string $emailAddress;
    public string $phoneNumber;
}

$data = [
    'first_name' => 'James',
    'last_name' => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number' => '+61 419 1234 5678',
];

$hydrator = new Hydrator\ObjectPropertyHydrator();
$user = $delegating->hydrate($data, new User());
$data = $delegating->extract(new User());
```

### ReflectionHydrator

The ReflectionHydrator is similar to the `ObjectPropertyHydrator`, however it uses [PHP's reflection API](http://php.net/manual/en/intro.reflection.php) to hydrate or extract properties of any visibility.
Any data key matching an existing property will be hydrated.
Any existing properties will be used for extracting data.

```php
<?php

use Laminas\Hydrator;

class User
{
    private string $firstName;
    private string $lastName;
    private string $emailAddress;
    private string $phoneNumber;
}

$data = [
    'first_name' => 'James',
    'last_name' => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number' => '+61 419 1234 5678',
];

$hydrator = new Hydrator\ReflectionHydrator();
$user = $delegating->hydrate($data, new User());
$data = $delegating->extract(new User());
```

### DelegatingHydrator

The DelegatingHydrator composes a hydrator locator, and will delegate `hydrate()` and `extract()` calls to the appropriate one based upon the class name of the object being operated on.

```php
<?php

// Instantiate each hydrator you wish to delegate to
$albumHydrator = new Laminas\Hydrator\ClassMethodsHydrator();
$artistHydrator = new Laminas\Hydrator\ClassMethodsHydrator();

// Map the entity class name to the hydrator using the HydratorPluginManager.
// In this case we have two entity classes, "Album" and "Artist".
$hydrators = new Laminas\Hydrator\HydratorPluginManager;
$hydrators->setService('Album', $albumHydrator);
$hydrators->setService('Artist', $artistHydrator);

// Create the DelegatingHydrator and tell it to use our configured hydrator locator
$delegating = new Laminas\Hydrator\DelegatingHydrator($hydrators);

// Now we can use $delegating to hydrate or extract any supported object
$array  = $delegating->extract(new Artist());
$artist = $delegating->hydrate($data, new Artist());
```

<a name="base_interfaces"></a>
## Base Interfaces

### ExtractionInterface

```php
<?php

namespace Laminas\Hydrator;

interface ExtractionInterface
{
    /**
     * Extract values from an object
     *
     * @return mixed[]
     */
    public function extract(object $object) : array;
}
```

### HydrationInterface

```php
<?php

namespace Laminas\Hydrator;

interface HydrationInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param mixed[] $data
     * @return object The implementation should return an object of any type.
     *     By purposely omitting the return type from the signature,
     *     implementations may choose to specify a more specific type.
     */
    public function hydrate(array $data, object $object);
}
```

### HydratorInterface

```php
<?php

namespace Laminas\Hydrator;

interface HydratorInterface extends
    ExtractionInterface,
    HydrationInterface
{
}
```
