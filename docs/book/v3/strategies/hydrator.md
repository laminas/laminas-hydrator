# Hydrator

The `HydratorStrategy` can be used to **hydrate an object and his child objects
with data from a nested array and vice versa**. 

## Basic usage

The following code example shows the isolated usage without adding the strategy
to a hydrator.

### Create and configure strategy

Create the strategy and set a hydrator and a classname for the handled object. 

```php
$strategy = new Laminas\Hydrator\Strategy\HydratorStrategy(
    new Laminas\Hydrator\ObjectPropertyHydrator(),
    stdClass::class
);
```

### Hydrate data

```php
$hydrated = $strategy->hydrate([
    'firstName' => 'David',
    'lastName'  => 'Bowie',
]);

echo $hydrated->firstName; // 'David'
echo $hydrated->lastName; // 'Bowie'
```

### Extract data

```php
$class            = new stdClass();
$class->firstName = 'David';
$class->lastName  = 'Bowie';

$extracted = $strategy->extract($class);

var_dump($extracted); // ['firstName' => 'David', 'lastName' => 'Bowie']
```

## Example

The following example shows the hydration for a class with a property that
consumes another class.

An example class which represents a music album.

```php
class Album
{
    private ?int $id = null;

    private ?string $title = null;

    private ?Artist $artist = null;

    public function __construct(
        ?int $id = null,
        ?string $title = null,
        ?Artist $artist = null
    ) {
        $this->id     = $id;
        $this->title  = $title;
        $this->artist = $artist;
    }

    public function getId() : ?int
    {
        return $this->id;
    }

    public function getTitle() : ?string
    {
        return $this->title;
    }

    public function getArtist() : ?Artist
    {
        return $this->artist;
    }
}
```

An example class representing the artist of an album.

```php
class Artist
{
    private ?string $firstName;

    private ?string $lastName;

    public function __construct(
        ?string $firstName = null,
        ?string $lastName = null
    ) {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
    }

    public function getFirstName() : ?string
    {
        return $this->firstName;
    }

    public function getLastName() : ?string
    {
        return $this->lastName;
    }
}
```

### Create hydrator and add strategy

Create a hydrator and add `HydratorStrategy` as strategy with a hydrator and a
classname for the handled object.

```php
$hydrator = new Laminas\Hydrator\ReflectionHydrator();
$hydrator->addStrategy(
    'artist',
    new Laminas\Hydrator\Strategy\HydratorStrategy(
        new Laminas\Hydrator\ReflectionHydrator(),
        Artist::class
    )
);
```

### Hydrate data

Create an instance of the example `Album` class and hydrate data.

```php
$album = new Album();
$hydrator->hydrate(
    [
        'id'     => 100,
        'title'  => 'The Next Day (Deluxe Version)',
        'artist' => [
            'firstName' => 'David',
            'lastName'  => 'Bowie',
        ],
    ],
    $album
);

echo $album->getTitle(); // 'The Next Day (Deluxe Version)'
echo $album->getArtist()->getFirstName(); // 'David'
echo $album->getArtist()->getLastName(); // 'Bowie'
```

### Extract data

```php
var_dump($hydrator->extract($album));
/*
array(3) {
  'id' =>
  int(100)
  'title' =>
  string(29) "The Next Day (Deluxe Version)"
  'artist' =>
  array(2) {
    'firstName' =>
    string(5) "David"
    'lastName' =>
    string(5) "Bowie"
  }
}
*/
```
