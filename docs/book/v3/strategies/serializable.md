# Serializable

The `SerializableStrategy` can be used for **serializing and deserializing PHP
types to and from different representations**.

The strategy uses [laminas-serializer](https://docs.laminas.dev/laminas-serializer/)
for serializing and deserializing of data.

## Basic usage

The following code example shows standalone usage without adding the strategy
to a hydrator.

### Create and configure strategy

Create the strategy and set a serializer adapter.

```php
$strategy = new Laminas\Hydrator\Strategy\SerializableStrategy(
    new Laminas\Serializer\Adapter\Json()
);
```

For available serializer adapters see the [documentation of laminas-serializer](https://docs.laminas.dev/laminas-serializer/adapter/).

### Hydrate data

```php
$json = '[
  {
    "title": "Modern Love",
    "duration": "4:46"
  },
  {
    "title": "China Girl",
    "duration": "5:32"
  }
]';
$hydrated = $strategy->hydrate($json);

echo $hydrated[1]['title']; // 'China Girl'
echo $hydrated[1]['duration']; // '5:32'
```

### Extract data

```php
$data = [
    [
        'title'    => 'Modern Love',
        'duration' => '4:46',
    ],
    [
        'title'    => 'China Girl',
        'duration' => '5:32',
    ],
    // …
];

$extracted = $strategy->extract($data);

echo $extracted; // '[{"title":"Modern Love","duration":"4:46"},{"title":"China Girl","duration":"5:32"}]'
```

## Example

The following example shows the hydration for a class with a property where the
data is provided by a JSON string.

An example class which represents a music album with tracks.

```php
class Album
{
    private ?string $title;

    private ?string $artist;

    private array $tracks;

    public function __construct(
        ?string $title = null,
        ?string $artist = null,
        array $tracks = []
    ) {
        $this->title  = $title;
        $this->artist = $artist;
        $this->tracks = $tracks;
    }

    public function getTitle() : ?string
    {
        return $this->title;
    }

    public function getArtist() : ?string
    {
        return $this->artist;
    }

    public function getTracks() : array
    {
        return $this->tracks;
    }
}
```

### Create hydrator and add strategy

Create a hydrator and add `SerializableStrategy` as a strategy, with a
serializer adapter which converts the JSON string to `array` and vice versa.

```php
$hydrator = new Laminas\Hydrator\ReflectionHydrator();
$hydrator->addStrategy(
    'tracks',
    new Laminas\Hydrator\Strategy\SerializableStrategy(
        new Laminas\Serializer\Adapter\Json()
    )
);
```

### Hydrate data

Create an instance of the example `Album` class and hydrate data.

```php
$album = new Album();
$hydrator->hydrate(
    [
        'artist' => 'David Bowie',
        'title'  => 'Let\'s Dance',
        'tracks' => '[{"title":"Modern Love","duration":"4:46"},{"title":"China Girl","duration":"5:32"}]',
    ],
    $album
);

echo $album->getTitle(); // "Let's Dance"
echo $album->getArtist(); // 'David Bowie'
echo $album->getTracks()[1]['title']; // 'China Girl'
echo $album->getTracks()[1]['duration']; // '5:32'
```

### Extract data

```php
var_dump($hydrator->extract($album));
/*
array(3) {
  'title' =>
  string(11) "Let's Dance"
  'artist' =>
  string(11) "David Bowie"
  'tracks' =>
  string(84) "[{"title":"Modern Love","duration":"4:46"},{"title":"China Girl","duration":"5:32"}]"
}
*/
```
