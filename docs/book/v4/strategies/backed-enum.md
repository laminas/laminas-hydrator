# BackedEnum

INFO: **New Feature**
Available since version 4.8.0

MISSING: **Installation Requirements**
[Enumerations](https://www.php.net/manual/language.enumerations.overview.php) require PHP version 8.1 or higher.

The `BackedEnumStrategy` provides **bidirectional conversion between strings
or integers and [Backed Enums](https://www.php.net/manual/en/language.enumerations.backed.php)**.

The code examples below will use the following backed enum, representing a
genre of music:

```php
enum Genre: string
{
    case Pop   = 'pop';
    case Blues = 'blues';
    case Jazz  = 'jazz';
}
```

## Basic Usage

The following code example shows standalone usage without adding the strategy
to a hydrator.

### Create and Configure Strategy

Create the strategy passing the class name of the enum it will hydrate and extract:

```php
$strategy = new Laminas\Hydrator\Strategy\BackedEnumStrategy(Genre::class);
```

### Hydrate Data

```php
$hydrated = $strategy->hydrate('blues', null);
var_dump($hydrated); // enum Genre::Blues : string("blues");
```

### Extract Data

```php
$extracted = $strategy->extract(Genre::Pop);
var_dump($extracted); // string(3) "pop"
```

## Example

The following example demonstrates hydration for a class with a property.

An example class which represents an album with a music genre:

```php
class Album
{
    private ?Genre $genre;

    public function __construct(?Genre $genre = null)
    {
        $this->genre = $genre;
    }

    public function getGenre() : Genre
    {
        return $this->genre;
    }
}
```

### Create Hydrator and Add Atrategy

Create a hydrator and add the `BackedEnumStrategy` as a strategy:

```php
$hydrator = new Laminas\Hydrator\ReflectionHydrator();
$hydrator->addStrategy(
    'genre',
    new Laminas\Hydrator\Strategy\BackedEnumStrategy(Genre::class)
);
```

### Hydrate Data

Create an instance of the example class and hydrate data:

```php
$album = new Album();
$hydrator->hydrate(['genre' => 'jazz'], $album);

echo $album->getGenre()->value; // "jazz"
```

### Extract Data

```php
$extracted = $hydrator->extract($album);

echo $extracted['genre']; // "jazz"
```
