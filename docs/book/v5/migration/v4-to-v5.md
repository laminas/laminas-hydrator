# Migration from Version 4 to 5

## Changed Behavior & Signature Changes

### Final Classes and Inheritance

As a best practice, even if a hydrator (or other classes) is not marked as `final` in this library, extending it is discouraged — future major releases may prohibit inheritance altogether.
Prefer **composition over inheritance** by combining small, focused objects.
Use **decorators** to extend behavior without modifying original classes or creating deep inheritance chains.

### Hydrator Plugin Manager

#### Removal of Legacy Zend Aliases

All aliases that referenced the equivalent, legacy "Zend" hydrators have been removed. This means that an exception will be thrown if you attempt to retrieve a hydrator using one of these aliases such as `Zend\Hydrator\ArraySerializableHydrator::class`.

You will need to either update your codebase to use known aliases such as `Laminas\Hydrator\ArraySerializableHydrator::class`, or re-implement the aliases in your configuration.

### Removed Exceptions

The following exceptions have been removed:

- `Laminas\Hydrator\Exception\ExtensionNotLoadedException`
- `Laminas\Hydrator\Exception\InvalidCallbackException`
- `Laminas\Hydrator\Exception\LogicException`

### Changes of `Laminas\Hydrator\Strategy\SerializableStrategy`

- Simplified implementation.
- Internal getter and setter methods have been removed.
- If you're relying on these, update your code to use serialization logic externally or via injected services.

## Removed Classes

The following deprecated classes (since version 3.0.0) have been removed in version 5.  
Please update your codebase to use the corresponding `*Hydrator` classes:

| Removed Class                             | Replacement Class                                  |
|-------------------------------------------|----------------------------------------------------|
| `Laminas\Hydrator\ArraySerializable`      | `Laminas\Hydrator\ArraySerializableHydrator`       |
| `Laminas\Hydrator\ClassMethods`           | `Laminas\Hydrator\ClassMethodsHydrator`            |
| `Laminas\Hydrator\ObjectProperty`         | `Laminas\Hydrator\ObjectPropertyHydrator`          |
| `Laminas\Hydrator\Reflection`             | `Laminas\Hydrator\ReflectionHydrator`              |

### `FilterComposite`: Integer Constants Replaced by `FilterCondition` Enum

The `FilterComposite::CONDITION_OR` and `FilterComposite::CONDITION_AND` integer constants have been removed.
Replace them with the new `FilterCondition` enum.

#### Before in Version 4

```php
use Laminas\Hydrator\Filter\FilterComposite;

$hydrator->addFilter('exclude', $myFilter, FilterComposite::CONDITION_AND);
```

#### Now in Version 5

```php
use Laminas\Hydrator\Filter\FilterCondition;

$hydrator->addFilter('exclude', $myFilter, FilterCondition::And);
```

The `$condition` parameter of `addFilter()` now requires a `FilterCondition` value instead of an `int` on `FilterComposite`, `FilterEnabledInterface`, `AbstractHydrator`, and `ClassMethodsHydrator`.

## Removed Features

### Removal of Module Manager Support

[Module Manager](https://docs.laminas.dev/laminas-modulemanager/) support has been removed along with the interface `Laminas\Hydrator\HydratorProviderInterface`.
