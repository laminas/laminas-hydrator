# Plugin Managers

It can be useful to compose a plugin manager from which you can retrieve
hydrators; in fact, `Laminas\Hydrator\DelegatingHydrator` does exactly that!
With such a manager, you can retrieve instances using short names, or instances
that have dependencies on other services, without needing to know the details of
how that works.

Examples of Hydrator plugin managers in real-world scenarios include:

- [hydrating database result sets](https://docs.laminas.dev/laminas-db/result-set/#laminas92db92resultset92hydratingresultset)
- [preparing API payloads](https://docs.mezzio.dev/mezzio-hal/resource-generator/#resourcegenerator)

## HydratorPluginManagerInterface

We provide two plugin manager implementations. Essentially, they only need to
implement the [PSR-11 ContainerInterface](https://www.php-fig.org/psr/psr-11/),
but plugin managers in current versions of [laminas-servicemanager](https://docs.laminas.dev/laminas-servicemanager/)
only implement it indirectly via the container-interop project.

As such, we ship `Laminas\Hydrator\HydratorPluginManagerInterface`, which simply
extends the PSR-11 `Psr\Container\ContainerInterface`. Each of our
implementations implement it.

## HydratorPluginManager

If you have used laminas-hydrator prior to version 3, you are likely already
familiar with this class, as it has been the implementation we have shipped from
initial versions. The `HydratorPluginManager` extends the laminas-servicemanager
`AbstractPluginManager`, and has the following behaviors:

- It will only return `Laminas\Hydrator\HydratorInterface` instances.
- It defines short-name aliases for all shipped hydrators (the class name minus
  the namespace), in a variety of casing combinations.
- All but the `DelegatingHydrator` are defined as invokable services (meaning
  they can be instantiated without any constructor arguments).
- The `DelegatingHydrator` is configured as a factory-based service, mapping to
  the `Laminas\Hydrator\DelegatingHydratorFactory`.
- No services are shared; a new instance is created each time you call `get()`.

### HydratorPluginManagerFactory

`Laminas\Hydrator\HydratorPluginManager` is mapped to the factory
`Laminas\Hydrator\HydratorPluginManagerFactory` when wired to the dependency
injection container.

The factory will look for the `config` service, and use the `hydrators`
configuration key to seed it with additional services. This configuration key
should map to an array that follows [standard laminas-servicemanager configuration](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/).

## StandaloneHydratorPluginManager

`Laminas\Hydrator\StandaloneHydratorPluginManager` provides an implementation that
has no dependencies on other libraries. **It can only load the hydrators shipped
with laminas-hydrator**.

### StandardHydratorPluginManagerFactory

`Laminas\Hydrator\StandardHydratorPluginManager` is mapped to the factory
`Laminas\Hydrator\StandardHydratorPluginManagerFactory` when wired to the dependency
injection container.

## HydratorManager alias

`Laminas\Hydrator\ConfigManager` defines an alias service, `HydratorManager`. That
service will point to `Laminas\Hydrator\HydratorPluginManager` if
laminas-servicemanager is installed, and `Laminas\Hydrator\StandaloneHydratorPluginManager`
otherwise.

## Custom plugin managers

If you do not want to use laminas-servicemanager, but want a plugin manager that is
customizable, or at least capable of loading the hydrators you have defined for
your application, you should write a custom implementation of
`Laminas\Hydrator\HydratorPluginManagerInterface`, and wire it to the
`HydratorManager` service, and/or one of the existing service names.

As an example, if you want a configurable solution that uses factories, and want
those factories capable of pulling application-level dependencies, you might do
something like the following:

Create a custom plugin manager class, e.g.
`src/YourApplication/CustomHydratorPluginManager.php`:

```php
namespace YourApplication;

use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\HydratorPluginManagerInterface;
use Laminas\Hydrator\StandaloneHydratorPluginManager;

class CustomHydratorPluginManager implements HydratorPluginManagerInterface
{
    /** @var ContainerInterface */
    private $appContainer;

    /** @var StandaloneHydratorPluginManager */
    private $defaults;

    /** @var array<string, string|callable> */
    private $factories = [];

    public function __construct(ContainerInterface $appContainer)
    {
        $this->appContainer = $appContainer;
        $this->defaults = new StandaloneHydratorPluginManager();
    }

    /**
     * {@inheritDoc}
     */
    public function get($id) : HydratorInterface
    {
        if (! isset($this->factories[$id]) && ! $this->defaults->has($id)) {
            $message = sprintf('Hydrator service %s not found', $id);
            throw new class($message) extends RuntimeException implements NotFoundExceptionInterface {};
        }

        // Default was requested; fallback to standalone container
        if (! isset($this->factories[$id])) {
            return $this->defaults->get($id);
        }

        $factory = $this->factories[$id];
        if (is_string($factory)) {
            $this->factories[$id] = $factory = new $factory();
        }

        return $factory($this->appContainer, $id);
    }

    public function has($id) : bool
    {
        return isset($this->factories[$id]) || $this->defaults->has($id);
    }

    public function setFactoryClass(string $name, string $factory) : void
    {
        $this->factories[$name] = $factory;
    }

    public function setFactory(string $name, callable $factory) : void
    {
        $this->factories[$name] = $factory;
    }
}
```

Create a factory for the custom plugin manager, e.g.
`src/YourApplication/CustomHydratorPluginManagerFactory.php`:

```php
namespace YourApplication;

use Psr\Container\ContainerInterface;

class CustomHydratorPluginManagerFactory
{
    public function __invoke(ContainerInterface $container) : CustomHydratorPluginManager
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = $config['hydrators']['factories'] ?? [];

        $manager = new CustomHydratorPluginManager($this);

        if ([] !== $config) {
            $this->configureManager($manager, $config);
        }

        return $manager;
    }

    /**
     * @param array<string, string|callable> $config
     */
    private function configureManager(CustomHydratorPluginManager $manager, array $config) : void
    {
        foreach ($config as $name => $factory) {
            is_string($factory)
                ? $manager->setFactoryClass($name, $factory)
                : $manager->setFactory($name, $factory);
        }
    }
}
```

Register the custom plugin manager in the application configuration, e.g.
`config/autoload/hydrators.global.php`:

```php
return [
    'dependencies' => [
        'aliases' => [
            'HydratorManager' => \YourApplication\CustomHydratorPluginManager::class,
        ],
        'factories' => [
            \YourApplication\CustomHydratorPluginManager::class => \YourApplication\CustomHydratorPluginManagerFactory::class
        ],
    ],
    'hydrators' => [
        'factories' => [
            \Blog\PostHydrator::class => \Blog\PostHydratorFactory::class,
            \News\ItemHydrator::class => \News\ItemHydratorFactory::class,
            // etc.
        ],
    ],
];
```
