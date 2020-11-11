# Migration from version 3

This document details changes made between version 3 and version 4 that could affect end-users.

## Interface changes

The `Laminas\Hydrator\Filter\FilterInterface::filter()` method changed signature to accept an optional second argument:

```php
namespace Laminas\Hydrator\Filter;

interface FilterInterface
{
    public function filter(string $property, ?object $instance = null) : bool;
}
```

The primary use case for this is when using anonymous objects, to facilitate reflection; the `ClassMethodsHydrator`, for instance, was updated to pass the `$instance` value only when an anonymous object is detected.
All filter implementations have been updated to the new signature.

## Filter changes

All filters implementing `Laminas\Hydrator\Filter\FilterInterface` shipped with the package are now marked final.
If you were previously extending these classes, you will need to copy and paste the implementations; if you feel there is a general-purpose use case for extending the class, please open a feature request to remove the `final` keyword on the specific implementation you are interested in.
