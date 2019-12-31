# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

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
