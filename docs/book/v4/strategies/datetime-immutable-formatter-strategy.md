# DateTimeImmutableFormatter

> Available since version 3.1.0

`DateTimeImmutableFormatterStrategy` provides **bidirectional conversion between
strings and `DateTimeImmutable` instances**.

The strategy uses `DateTimeFormatterStrategy` for conversion where the
[input and output formats](../strategy.md#laminas-hydrator-strategy-datetimeformatterstrategy)
can be set.

## Basic Usage

The following code example shows standalone usage without adding the strategy
to a hydrator.

### Create and configure strategy

Create the strategy and set the [input and output formats](../strategy.md#laminas-hydrator-strategy-datetimeformatterstrategy)
via the `DateTimeFormatterStrategy`.

```php
$strategy = new Laminas\Hydrator\Strategy\DateTimeImmutableFormatterStrategy(
    new Laminas\Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d')
);
```

### Hydrate data

```php
$hydrated = $strategy->hydrate('2020-07-01');

var_dump($hydrated instanceof DateTimeImmutable); // true
```

### Extract data

```php
$extracted = $strategy->extract(
    DateTimeImmutable::createFromFormat('Y-m-d', '2020-07-01')
);

echo $extracted // '2020-07-01'
```

## Example

The following example demonstrates hydration for a class with a property.

An example class which represents a music album with a release date:

```php
class Album
{
    private ?DateTimeImmutable $releaseDate;

    public function __construct(?DateTimeImmutable $releaseDate = null)
    {
        $this->releaseDate = $releaseDate;
    }

    public function getReleaseDate() : ?DateTimeImmutable
    {
        return $this->releaseDate;
    }
}
```

### Create hydrator and add strategy

Create a hydrator and add `DateTimeImmutableFormatterStrategy` as strategy:

```php
$hydrator = new Laminas\Hydrator\ReflectionHydrator();
$hydrator->addStrategy(
    'releaseDate',
    new Laminas\Hydrator\Strategy\DateTimeImmutableFormatterStrategy(
        new Laminas\Hydrator\Strategy\DateTimeFormatterStrategy('Y-m-d')
    )
);
```

### Hydrate data

Create an instance of the example class and hydrate data:

```php
$album = new Album();
$hydrator->hydrate(['releaseDate' => '2020-07-01'], $album);

var_dump($album->getReleaseDate() instanceof DateTimeImmutable); // true
```

### Extract data

```php
$extracted = $hydrator->extract($album);

echo $extracted; // '2020-07-01'
```
