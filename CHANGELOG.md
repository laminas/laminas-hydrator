# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.2.2 - 2017-05-17

### Added

### Changes

- [zendframework/zend-hydrator#42](https://github.com/zendframework/zend-hydrator/pull/42) updates the
  `ConfigProvider::getDependencies()` method to map the `HydratorPluginManager`
  class to the `HydratorPluginManagerFactory` class, and make the
  `HydratorManager` service an alias to the fully-qualified
  `HydratorPluginManager` class.
- [zendframework/zend-hydrator#45](https://github.com/zendframework/zend-hydrator/pull/45) changes the
  `ClassMethods` hydrator to take into account naming strategies when present,
  making it act consistently with the other hydrators.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-hydrator#59](https://github.com/zendframework/zend-hydrator/pull/59) fixes how the
  `HydratorPluginManagerFactory` factory initializes the plugin manager
  instance, ensuring it is injecting the relevant configuration from the
  `config` service and thus seeding it with configured hydrator services. This
  means that the `hydrators` configuration will now be honored in non-laminas-mvc
  contexts.

## 2.2.1 - 2016-04-18

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-hydrator#28](https://github.com/zendframework/zend-hydrator/pull/28) fixes the
  `Module::init()` method to properly receive a `ModuleManager` instance, and
  not expect a `ModuleEvent`.

## 2.2.0 - 2016-04-06

### Added

- [zendframework/zend-hydrator#26](https://github.com/zendframework/zend-hydrator/pull/26) exposes the
  package as a Laminas component and/or generic configuration provider, by adding the
  following:
  - `HydratorPluginManagerFactory`, which can be consumed by container-interop /
    laminas-servicemanager to create and return a `HydratorPluginManager` instance.
  - `ConfigProvider`, which maps the service `HydratorManager` to the above
    factory.
  - `Module`, which does the same as `ConfigProvider`, but specifically for
    laminas-mvc applications. It also provices a specification to
    `Laminas\ModuleManager\Listener\ServiceListener` to allow modules to provide
    hydrator configuration.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.1.0 - 2016-02-18

### Added

- [zendframework/zend-hydrator#20](https://github.com/zendframework/zend-hydrator/pull/20) imports the
  documentation from laminas-stdlib, publishes it to
  https://docs.laminas.dev/laminas-hydrator/, and automates building and
  publishing the documentation.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-hydrator#6](https://github.com/zendframework/zend-hydrator/pull/6) add additional
  unit test coverage
- [zendframework/zend-hydrator#17](https://github.com/zendframework/zend-hydrator/pull/17) and
  [zendframework/zend-hydrator#23](https://github.com/zendframework/zend-hydrator/pull/23) update the code
  to be forwards compatible with laminas-servicemanager v3, and to depend on
  laminas-stdlib and laminas-eventmanager v3.

## 2.0.0 - 2015-09-17

### Added

- The following classes were marked `final` (per their original implementation
  in laminas-stdlib):
  - `Laminas\Hydrator\NamingStrategy\IdentityNamingStrategy`
  - `Laminas\Hydrator\NamingStrategy\ArrayMapNamingStrategy`
  - `Laminas\Hydrator\NamingStrategy\CompositeNamingStrategy`
  - `Laminas\Hydrator\Strategy\ExplodeStrategy`
  - `Laminas\Hydrator\Strategy\StrategyChain`
  - `Laminas\Hydrator\Strategy\DateTimeFormatterStrategy`
  - `Laminas\Hydrator\Strategy\BooleanStrategy`

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.0 - 2015-09-17

Initial release. This ports all hydrator classes and functionality from
[laminas-stdlib](https://github.com/laminas/laminas-stdlib) to a standalone
repository. All final keywords are removed, to allow a deprecation cycle in the
laminas-stdlib component.

Please note: the following classes will be marked as `final` for a version 2.0.0
release to immediately follow 1.0.0:

- `Laminas\Hydrator\NamingStrategy\IdentityNamingStrategy`
- `Laminas\Hydrator\NamingStrategy\ArrayMapNamingStrategy`
- `Laminas\Hydrator\NamingStrategy\CompositeNamingStrategy`
- `Laminas\Hydrator\Strategy\ExplodeStrategy`
- `Laminas\Hydrator\Strategy\StrategyChain`
- `Laminas\Hydrator\Strategy\DateTimeFormatterStrategy`
- `Laminas\Hydrator\Strategy\BooleanStrategy`

As such, you should not extend them.

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
