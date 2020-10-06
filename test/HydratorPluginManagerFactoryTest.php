<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Interop\Container\ContainerInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\Hydrator\HydratorPluginManagerFactory;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class HydratorPluginManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $factory = new HydratorPluginManagerFactory();

        $hydrators = $factory($container, HydratorPluginManagerFactory::class);
        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
    }

    /**
     * @depends testFactoryReturnsPluginManager
     *
     * @return void
     */
    public function testFactoryConfiguresPluginManagerUnderContainerInterop(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $hydrator = $this->createMock(HydratorInterface::class);

        $factory = new HydratorPluginManagerFactory();
        $hydrators = $factory($container, HydratorPluginManagerFactory::class, [
            'services' => [
                'test' => $hydrator,
            ],
        ]);
        $this->assertSame($hydrator, $hydrators->get('test'));
    }

    public function testConfiguresHydratorServicesWhenFound(): void
    {
        $hydrator = $this->createMock(HydratorInterface::class);
        $config = [
            'hydrators' => [
                'aliases' => [
                    'test' => ReflectionHydrator::class,
                ],
                'factories' => [
                    /** @psalm-return \PHPUnit\Framework\MockObject\MockObject&HydratorInterface */
                    'test-too' => function ($container) use ($hydrator): HydratorInterface {
                        return $hydrator;
                    },
                ],
            ],
        ];

        $container = $this->createMock(ServiceLocatorInterface::class);
        $container
            ->expects($this->exactly(2))
            ->method('has')
            ->withConsecutive(
                ['ServiceListener'],
                ['config']
            )
            ->willReturnOnConsecutiveCalls(
                false,
                true
            );
        $container
            ->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new HydratorPluginManagerFactory();
        $hydrators = $factory($container, 'HydratorManager');

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
        $this->assertTrue($hydrators->has('test'));
        $this->assertInstanceOf(ReflectionHydrator::class, $hydrators->get('test'));
        $this->assertTrue($hydrators->has('test-too'));
        $this->assertSame($hydrator, $hydrators->get('test-too'));
    }

    public function testDoesNotConfigureHydratorServicesWhenServiceListenerPresent(): void
    {
        $container = $this->createMock(ServiceLocatorInterface::class);
        $container
            ->expects($this->exactly(2))
            ->method('has')
            ->withConsecutive(
                ['ServiceListener'],
                ['config']
            )
            ->willReturnOnConsecutiveCalls(
                false,
                false
            );
        $container
            ->expects($this->never())
            ->method('get');

        $factory = new HydratorPluginManagerFactory();
        $hydrators = $factory($container, 'HydratorManager');

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
        $this->assertFalse($hydrators->has('test'));
        $this->assertFalse($hydrators->has('test-too'));
    }

    public function testDoesNotConfigureHydratorServicesWhenConfigServiceNotPresent(): void
    {
        $container = $this->createMock(ServiceLocatorInterface::class);
        $container
            ->expects($this->exactly(2))
            ->method('has')
            ->withConsecutive(
                ['ServiceListener'],
                ['config']
            )
            ->willReturnOnConsecutiveCalls(
                false,
                false
            );
        $container
            ->expects($this->never())
            ->method('get');

        $factory = new HydratorPluginManagerFactory();
        $hydrators = $factory($container, 'HydratorManager');

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
    }

    public function testDoesNotConfigureHydratorServicesWhenConfigServiceDoesNotContainHydratorsConfig(): void
    {
        $container = $this->createMock(ServiceLocatorInterface::class);
        $container
            ->expects($this->exactly(2))
            ->method('has')
            ->withConsecutive(
                ['ServiceListener'],
                ['config']
            )
            ->willReturnOnConsecutiveCalls(
                false,
                true
            );
        $container
            ->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn(['foo' => 'bar']);

        $factory = new HydratorPluginManagerFactory();
        $hydrators = $factory($container, 'HydratorManager');

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
        $this->assertFalse($hydrators->has('foo'));
    }
}
