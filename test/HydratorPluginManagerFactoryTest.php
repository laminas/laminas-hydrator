<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\Hydrator\HydratorPluginManagerFactory;
use Laminas\Hydrator\ReflectionHydrator;
use LaminasTest\Hydrator\TestAsset\InMemoryContainer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @psalm-suppress InternalClass
 */
#[CoversClass(HydratorPluginManagerFactory::class)]
final class HydratorPluginManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager(): void
    {
        $factory   = new HydratorPluginManagerFactory();
        $hydrators = $factory(new InMemoryContainer());

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
    }

    public function testConfiguresHydratorServicesWhenFound(): void
    {
        $hydrator  = $this->createStub(HydratorInterface::class);
        $config    = [
            'hydrators' => [
                'aliases'   => [
                    'test' => ReflectionHydrator::class,
                ],
                'factories' => [
                    'test-too' => static fn(): Stub&HydratorInterface => $hydrator,
                ],
            ],
        ];
        $container = new InMemoryContainer();
        $container->set('config', $config);

        $factory   = new HydratorPluginManagerFactory();
        $hydrators = $factory($container);

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
        $hydrators = $factory($container);

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
        $this->assertFalse($hydrators->has('test'));
        $this->assertFalse($hydrators->has('test-too'));
    }

    public function testDoesNotConfigureHydratorServicesWhenConfigServiceNotPresent(): void
    {
        $container = new InMemoryContainer();
        $factory   = new HydratorPluginManagerFactory();
        $hydrators = $factory($container);

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
    }

    public function testDoesNotConfigureHydratorServicesWhenConfigServiceDoesNotContainHydratorsConfig(): void
    {
        $container = new InMemoryContainer();
        $container->set('config', ['foo' => 'bar']);

        $factory   = new HydratorPluginManagerFactory();
        $hydrators = $factory($container);

        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);
        $this->assertFalse($hydrators->has('foo'));
    }
}
