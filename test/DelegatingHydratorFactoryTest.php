<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Interop\Container\ContainerInterface;
use Laminas\Hydrator\DelegatingHydrator;
use Laminas\Hydrator\DelegatingHydratorFactory;
use Laminas\Hydrator\HydratorPluginManager;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @covers Laminas\Hydrator\DelegatingHydratorFactory
 */
class DelegatingHydratorFactoryTest extends TestCase
{
    public function testFactoryUsesContainerToSeedDelegatingHydratorWhenItIsAHydratorPluginManager(): void
    {
        $hydrators = $this->createMock(HydratorPluginManager::class);
        $factory   = new DelegatingHydratorFactory();

        $hydrator = $factory($hydrators, '');
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);
    }

    // phpcs:ignore Generic.Files.LineLength.TooLong
    public function testFactoryUsesHydratorPluginManagerServiceFromContainerToSeedDelegatingHydratorWhenAvailable(): void
    {
        // phpcs:enable
        $hydrators = $this->createMock(HydratorPluginManager::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('has')->with(HydratorPluginManager::class)->willReturn(true);
        $container->expects($this->once())->method('get')->with(HydratorPluginManager::class)->willReturn($hydrators);

        $factory = new DelegatingHydratorFactory();

        $hydrator = $factory($container, '');
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);
    }

    public function testFactoryUsesHydratorManagerServiceFromContainerToSeedDelegatingHydratorWhenAvailable(): void
    {
        $hydrators = $this->createMock(HydratorPluginManager::class);
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->exactly(3))
            ->method('has')
            ->withConsecutive(
                [HydratorPluginManager::class],
                [\Zend\Hydrator\HydratorPluginManager::class],
                ['HydratorManager']
            )
            ->willReturnOnConsecutiveCalls(
                false,
                false,
                true
            );
        $container->expects($this->once())->method('get')->with('HydratorManager')->willReturn($hydrators);

        $factory = new DelegatingHydratorFactory();

        $hydrator = $factory($container, '');
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);
    }

    public function testFactoryCreatesHydratorPluginManagerToSeedDelegatingHydratorAsFallback(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->exactly(3))
            ->method('has')
            ->withConsecutive(
                [HydratorPluginManager::class],
                [\Zend\Hydrator\HydratorPluginManager::class],
                ['HydratorManager']
            )
            ->willReturnOnConsecutiveCalls(
                false,
                false,
                false
            );
        $container->expects($this->never())->method('get');

        $factory = new DelegatingHydratorFactory();

        $hydrator = $factory($container, '');
        $this->assertInstanceOf(DelegatingHydrator::class, $hydrator);

        $r = new ReflectionProperty($hydrator, 'hydrators');
        $r->setAccessible(true);
        $hydrators = $r->getValue($hydrator);

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
    }
}
