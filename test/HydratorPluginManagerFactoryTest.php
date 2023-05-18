<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\Hydrator\HydratorPluginManagerFactory;
use Laminas\Hydrator\ReflectionHydrator;
use LaminasTest\Hydrator\TestAsset\InMemoryContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class HydratorPluginManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory   = new HydratorPluginManagerFactory();

        $hydrators = $factory($container, HydratorPluginManagerFactory::class);
        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderContainerInterop(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $hydrator  = $this->createMock(HydratorInterface::class);

        $factory   = new HydratorPluginManagerFactory();
        $hydrators = $factory($container, HydratorPluginManagerFactory::class, [
            'services' => [
                'test' => $hydrator,
            ],
        ]);
        $this->assertSame($hydrator, $hydrators->get('test'));
    }

    public function testConfiguresHydratorServicesWhenFound(): void
    {
        $hydrator  = $this->createMock(HydratorInterface::class);
        $config    = [
            'hydrators' => [
                'aliases'   => [
                    'test' => ReflectionHydrator::class,
                ],
                'factories' => [
                    /** @psalm-return MockObject&HydratorInterface */
                    'test-too' => static fn(): HydratorInterface => $hydrator,
                ],
            ],
        ];
        $container = new InMemoryContainer();
        $container->set('config', $config);

        $factory   = new HydratorPluginManagerFactory();
        $hydrators = $factory($container, 'HydratorManager');

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
        $this->assertTrue($hydrators->has('test'));
        $this->assertInstanceOf(ReflectionHydrator::class, $hydrators->get('test'));
        $this->assertTrue($hydrators->has('test-too'));
        $this->assertSame($hydrator, $hydrators->get('test-too'));
    }

    public function testDoesNotConfigureHydratorServicesWhenServiceListenerPresent(): void
    {
        $container = new InMemoryContainer();
        $factory   = new HydratorPluginManagerFactory();
        $hydrators = $factory($container, 'HydratorManager');

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
        $this->assertFalse($hydrators->has('test'));
        $this->assertFalse($hydrators->has('test-too'));
    }

    public function testDoesNotConfigureHydratorServicesWhenConfigServiceNotPresent(): void
    {
        $container = new InMemoryContainer();
        $factory   = new HydratorPluginManagerFactory();
        $hydrators = $factory($container, 'HydratorManager');

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
    }

    public function testDoesNotConfigureHydratorServicesWhenConfigServiceDoesNotContainHydratorsConfig(): void
    {
        $container = new InMemoryContainer();
        $container->set('config', ['foo' => 'bar']);

        $factory   = new HydratorPluginManagerFactory();
        $hydrators = $factory($container, 'HydratorManager');

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
        $this->assertFalse($hydrators->has('foo'));
    }
}
