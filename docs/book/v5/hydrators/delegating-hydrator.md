# DelegatingHydrator

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
