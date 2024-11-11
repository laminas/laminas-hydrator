# Collection

The `CollectionStrategy` can be used to **hydrate a collection of objects
with data from an array with multiple items and vice versa**.

The strategy uses a hydrator to hydrate and extract data from each item of a
collection.

## Basic usage

The following code example shows standalone usage without adding the strategy
to a hydrator.

### Create and configure strategy

Create the strategy and set a hydrator and a classname for the handled object
items.

```php
$strategy = new Laminas\Hydrator\Strategy\CollectionStrategy(
    new Laminas\Hydrator\ObjectPropertyHydrator(),
    stdClass::class
);
```

### Hydrate data

```php
$hydrated = $strategy->hydrate([
    [
        'title'    => 'Modern Love',
        'duration' => '4:46',
    ],
    [
        'title'    => 'China Girl',
        'duration' => '5:32',
    ],
    // …
]);

echo $hydrated[1]->title; // 'China Girl'
echo $hydrated[1]->duration; // '5:32'
```

### Extract data

```php
// Define array with objects
$track1           = new stdClass();
$track1->title    = 'Modern Love';
$track1->duration = '4:46';

$track2           = new stdClass();
$track2->title    = 'China Girl';
$track2->duration = '5:32';

$data = [
    $track1,
    $track2,
];

// Extract
$extracted = $strategy->extract($data);

var_dump($extracted);
/*
array(2) {
  [0] =>
  array(2) {
    'title' =>
    string(11) "Modern Love"
    'duration' =>
    string(4) "4:46"
  }
  [1] =>
  array(2) {
    'title' =>
    string(10) "China Girl"
    'duration' =>
    string(4) "5:32"
  }
}
*/
```

## Example

The following example shows the hydration for a class with a property that
consumes array of classes.

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

An example class representing a track of an album.

```php
class Track
{
    private ?string $title;

    private ?string $duration;

    public function __construct(
        ?string $title = null,
        ?string $duration = null
    ) {
        $this->title    = $title;
        $this->duration = $duration;
    }

    public function getTitle() : ?string
    {
        return $this->title;
    }

    public function getDuration() : ?string
    {
        return $this->duration;
    }
}
```

### Create hydrator and add strategy

Create a hydrator and add `CollectionStrategy` as a strategy, with a hydrator
and a classname for the handled object items.

```php
$hydrator = new Laminas\Hydrator\ReflectionHydrator();
$hydrator->addStrategy(
    'tracks',
    new Laminas\Hydrator\Strategy\CollectionStrategy(
        new Laminas\Hydrator\ReflectionHydrator(),
        Track::class
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
        'tracks' => [
            [
                'title'    => 'Modern Love',
                'duration' => '4:46',
            ],
            [
                'title'    => 'China Girl',
                'duration' => '5:32',
            ],
            [
                'title'    => 'Let\'s Dance',
                'duration' => '7:38',
            ],
            // …
        ],
    ],
    $album
);

echo $album->getTitle(); // "Let's Dance"
echo $album->getArtist(); // 'David Bowie'
echo $album->getTracks()[1]->getTitle(); // 'China Girl'
echo $album->getTracks()[1]->getDuration(); // '5:32'
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
  array(3) {
    [0] =>
    array(2) {
      'title' =>
      string(11) "Modern Love"
      'duration' =>
      string(4) "4:46"
    }
    [1] =>
    array(2) {
      'title' =>
      string(10) "China Girl"
      'duration' =>
      string(4) "5:32"
    }
    [2] =>
    array(2) {
      'title' =>
      string(11) "Let's Dance"
      'duration' =>
      string(4) "7:38"
    }
  }
} 
*/
```
