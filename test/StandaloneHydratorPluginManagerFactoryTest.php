<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ArraySerializable;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\DelegatingHydrator;
use Laminas\Hydrator\ObjectProperty;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\Hydrator\Reflection;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\StandaloneHydratorPluginManager;
use Laminas\Hydrator\StandaloneHydratorPluginManagerFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

use function sprintf;

class StandaloneHydratorPluginManagerFactoryTest extends TestCase
{
    private const MESSAGE_DEFAULT_SERVICES = 'Missing the service %s';

    protected function setUp(): void
    {
        $this->factory   = new StandaloneHydratorPluginManagerFactory();
        $this->container = $this->createMock(ContainerInterface::class);
    }

    public function assertDefaultServices(
        StandaloneHydratorPluginManager $manager,
        string $message = self::MESSAGE_DEFAULT_SERVICES
    ): void {
        $this->assertTrue($manager->has('ArraySerializable'), sprintf($message, 'ArraySerializable'));
        $this->assertTrue($manager->has('ArraySerializableHydrator'), sprintf($message, 'ArraySerializableHydrator'));
        $this->assertTrue($manager->has(ArraySerializable::class), sprintf($message, ArraySerializable::class));
        $this->assertTrue(
            $manager->has(ArraySerializableHydrator::class),
            sprintf($message, ArraySerializableHydrator::class)
        );

        $this->assertTrue($manager->has('ClassMethods'), sprintf($message, 'ClassMethods'));
        $this->assertTrue($manager->has('ClassMethodsHydrator'), sprintf($message, 'ClassMethodsHydrator'));
        $this->assertTrue($manager->has(ClassMethods::class), sprintf($message, ClassMethods::class));
        $this->assertTrue($manager->has(ClassMethodsHydrator::class), sprintf($message, ClassMethodsHydrator::class));

        $this->assertTrue($manager->has('DelegatingHydrator'), sprintf($message, 'DelegatingHydrator'));
        $this->assertTrue($manager->has(DelegatingHydrator::class), sprintf($message, DelegatingHydrator::class));

        $this->assertTrue($manager->has('ObjectProperty'), sprintf($message, 'ObjectProperty'));
        $this->assertTrue($manager->has('ObjectPropertyHydrator'), sprintf($message, 'ObjectPropertyHydrator'));
        $this->assertTrue($manager->has(ObjectProperty::class), sprintf($message, ObjectProperty::class));
        $this->assertTrue(
            $manager->has(ObjectPropertyHydrator::class),
            sprintf($message, ObjectPropertyHydrator::class)
        );

        $this->assertTrue($manager->has('Reflection'), sprintf($message, 'Reflection'));
        $this->assertTrue($manager->has('ReflectionHydrator'), sprintf($message, 'ReflectionHydrator'));
        $this->assertTrue($manager->has(Reflection::class), sprintf($message, Reflection::class));
        $this->assertTrue($manager->has(ReflectionHydrator::class), sprintf($message, ReflectionHydrator::class));
    }

    public function testCreatesPluginManagerWithDefaultServices(): void
    {
        $manager = ($this->factory)($this->container);
        $this->assertDefaultServices($manager);
    }
}
