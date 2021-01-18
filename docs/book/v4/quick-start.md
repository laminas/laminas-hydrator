# Quick Start

The laminas-hydrator component provides functionality for hydrating objects (which is the act of populating an object from a set of data) and extracting data from them.

The component contains [concrete implementations](#available_implementations) for a number of common use cases, such as by using arrays, object methods, and reflection, and provides [interfaces](#base_interfaces) for creating custom implementations.

## Basic Usage

### Hydrating an Object

To hydrate an object with data, instantiate the hydrator and then pass to it the data for hydrating the object.

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

### Extracting Values From an Object

To extract data from an object, instantiate the applicable hydrator and then call `extract`, passing to it the object to extract data from.

```php
$hydrator = new Laminas\Hydrator\ArraySerializableHydrator();

// ... Assuming that $object has already been initialised
$data = $hydrator->extract($object);
```

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
class User
{
    private $firstName;
    private $lastName;
    private $emailAddress;
    private $phoneNumber;

    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    public function setEmailAddress(string $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    public function setPhoneNumber(string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }
}

$data = [
    'first_name'    => 'James',
    'last_name'     => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number'  => '+61 419 1234 5678',
];

$hydrator = new Laminas\Hydrator\ClassMethodsHydrator();
$user     = $hydrator->hydrate($data, new User());
$data     = $hydrator->extract(new User());
```

### ObjectPropertyHydrator

The ObjectPropertyHydrator hydrates objects and extracts data using publicly accessible properties which match a key in the data set.

```php
class User
{
    public $firstName;
    public $lastName;
    public $emailAddress;
    public $phoneNumber;
}

$data = [
    'first_name'    => 'James',
    'last_name'     => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number'  => '+61 419 1234 5678',
];

$hydrator = new Laminas\Hydrator\ObjectPropertyHydrator();
$user     = $hydrator->hydrate($data, new User());
$data     = $hydrator->extract(new User());
```

### ReflectionHydrator

The ReflectionHydrator is similar to the `ObjectPropertyHydrator`, however it uses [PHP's reflection API](http://php.net/manual/en/intro.reflection.php) to hydrate or extract properties of any visibility.
Any data key matching an existing property will be hydrated.
Any existing properties will be used for extracting data.

```php
class User
{
    private $firstName;
    private $lastName;
    private $emailAddress;
    private $phoneNumber;
}

$data = [
    'first_name'    => 'James',
    'last_name'     => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number'  => '+61 419 1234 5678',
];

$hydrator = new Laminas\Hydrator\ReflectionHydrator();
$user     = $hydrator->hydrate($data, new User());
$data     = $hydrator->extract(new User());
```

### DelegatingHydrator

The DelegatingHydrator composes a hydrator locator, and will delegate `hydrate()` and `extract()` calls to the appropriate one based upon the class name of the object being operated on.

```php
// Instantiate each hydrator you wish to delegate to
$albumHydrator  = new Laminas\Hydrator\ClassMethodsHydrator();
$artistHydrator = new Laminas\Hydrator\ClassMethodsHydrator();

// Map the entity class name to the hydrator using the HydratorPluginManager.
// In this case we have two entity classes, "Album" and "Artist".
$hydrators = new Laminas\Hydrator\HydratorPluginManager;
$hydrators->setService('Album', $albumHydrator);
$hydrators->setService('Artist', $artistHydrator);

// Create the DelegatingHydrator and tell it to use our configured hydrator locator
$delegating = new Laminas\Hydrator\DelegatingHydrator($hydrators);

// Now we can use $delegating to hydrate or extract any supported object
// Assumes that $data and Artist have already been initialised
$array  = $delegating->extract(new Artist());
$artist = $delegating->hydrate($data, new Artist());
```
