<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\DelegatingHydrator;
use Laminas\Hydrator\DelegatingHydratorFactory;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\Hydrator\HydratorPluginManagerInterface;
use LaminasTest\Hydrator\TestAsset\InMemoryContainer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use ReflectionProperty;

#[CoversClass(DelegatingHydratorFactory::class)]
final class DelegatingHydratorFactoryTest extends TestCase
{
    public function testFactoryUsesContainerToSeedDelegatingHydratorWhenItIsAHydratorPluginManager(): void
    {
        $hydrators = $this->createMock(HydratorPluginManagerInterface::class);
        $factory   = new DelegatingHydratorFactory();

        $hydrator = $factory($hydrators);
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);
    }

    // phpcs:ignore Generic.Files.LineLength.TooLong
    public function testFactoryUsesHydratorPluginManagerServiceFromContainerToSeedDelegatingHydratorWhenAvailable(): void
    {
        // phpcs:enable
        $hydrators = $this->createMock(HydratorPluginManagerInterface::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with(HydratorPluginManager::class)->willReturn(true);
        $container->expects($this->once())->method('get')->with(HydratorPluginManager::class)->willReturn($hydrators);

        $factory = new DelegatingHydratorFactory();

        $hydrator = $factory($container);
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);
    }

    public function testFactoryUsesHydratorManagerServiceFromContainerToSeedDelegatingHydratorWhenAvailable(): void
    {
        $hydrators = $this->createMock(HydratorPluginManagerInterface::class);
        $container = new InMemoryContainer();
        $container->set('HydratorManager', $hydrators);
        $factory = new DelegatingHydratorFactory();

        $hydrator = $factory($container);
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);
    }

    public function testFactoryCreatesHydratorPluginManagerToSeedDelegatingHydratorAsFallback(): void
    {
        $container = new InMemoryContainer();
        $factory   = new DelegatingHydratorFactory();

        $hydrator = $factory($container);
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);

        $r         = new ReflectionProperty($hydrator, 'hydrators');
        $hydrators = $r->getValue($hydrator);

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
    }
}
