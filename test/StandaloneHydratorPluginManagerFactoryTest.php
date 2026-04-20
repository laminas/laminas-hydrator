<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\DelegatingHydrator;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\StandaloneHydratorPluginManager;
use Laminas\Hydrator\StandaloneHydratorPluginManagerFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Reflection;

use function sprintf;

#[CoversClass(StandaloneHydratorPluginManagerFactory::class)]
final class StandaloneHydratorPluginManagerFactoryTest extends TestCase
{
    private const MESSAGE_DEFAULT_SERVICES = 'Missing the service %s';

    private StandaloneHydratorPluginManagerFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new StandaloneHydratorPluginManagerFactory();
    }

    public function assertDefaultServices(
        StandaloneHydratorPluginManager $manager,
        string $message = self::MESSAGE_DEFAULT_SERVICES
    ): void {
        $this->assertTrue($manager->has('ArraySerializable'), sprintf($message, 'ArraySerializable'));
        $this->assertTrue($manager->has('ArraySerializableHydrator'), sprintf($message, 'ArraySerializableHydrator'));
        $this->assertTrue(
            $manager->has(ArraySerializableHydrator::class),
            sprintf($message, ArraySerializableHydrator::class)
        );

        $this->assertTrue($manager->has('ClassMethods'), sprintf($message, 'ClassMethods'));
        $this->assertTrue($manager->has('ClassMethodsHydrator'), sprintf($message, 'ClassMethodsHydrator'));
        $this->assertTrue($manager->has(ClassMethodsHydrator::class), sprintf($message, ClassMethodsHydrator::class));

        $this->assertTrue($manager->has('DelegatingHydrator'), sprintf($message, 'DelegatingHydrator'));
        $this->assertTrue($manager->has(DelegatingHydrator::class), sprintf($message, DelegatingHydrator::class));

        $this->assertTrue($manager->has('ObjectProperty'), sprintf($message, 'ObjectProperty'));
        $this->assertTrue($manager->has('ObjectPropertyHydrator'), sprintf($message, 'ObjectPropertyHydrator'));
        $this->assertTrue(
            $manager->has(ObjectPropertyHydrator::class),
            sprintf($message, ObjectPropertyHydrator::class)
        );

        $this->assertTrue($manager->has(Reflection::class), sprintf($message, Reflection::class));
        $this->assertTrue($manager->has('ReflectionHydrator'), sprintf($message, 'ReflectionHydrator'));
        $this->assertTrue($manager->has(ReflectionHydrator::class), sprintf($message, ReflectionHydrator::class));
    }

    public function testCreatesPluginManagerWithDefaultServices(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->createStub(ContainerInterface::class);
        $manager   = ($this->factory)($container);
        $this->assertDefaultServices($manager);
    }
}
