<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Closure;
use Generator;
use Laminas\Hydrator;
use Laminas\Hydrator\StandaloneHydratorPluginManager;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

use function array_pop;
use function sprintf;

class StandaloneHydratorPluginManagerTest extends TestCase
{
    private StandaloneHydratorPluginManager $manager;

    protected function setUp(): void
    {
        $this->manager = new StandaloneHydratorPluginManager();
    }

    /**
     * @return mixed
     */
    public function reflectProperty(object $class, string $property)
    {
        $r = new ReflectionProperty($class, $property);
        $r->setAccessible(true);
        return $r->getValue($class);
    }

    /**
     * @psalm-return Generator<string, array{0: class-string}>
     */
    public function hydratorsWithoutConstructors(): Generator
    {
        yield 'ArraySerializable'               => [Hydrator\ArraySerializableHydrator::class];
        yield 'ArraySerializableHydrator'       => [Hydrator\ArraySerializableHydrator::class];
        yield 'ClassMethods'                    => [Hydrator\ClassMethodsHydrator::class];
        yield 'ClassMethodsHydrator'            => [Hydrator\ClassMethodsHydrator::class];
        yield Hydrator\ArraySerializable::class => [Hydrator\ArraySerializableHydrator::class];
        yield Hydrator\ClassMethods::class      => [Hydrator\ClassMethodsHydrator::class];
        yield Hydrator\ObjectProperty::class    => [Hydrator\ObjectPropertyHydrator::class];
        yield Hydrator\Reflection::class        => [Hydrator\ReflectionHydrator::class];
        yield 'ObjectPropertyHydrator'          => [Hydrator\ObjectPropertyHydrator::class];
        yield 'ObjectProperty'                  => [Hydrator\ObjectPropertyHydrator::class];
        yield 'ReflectionHydrator'              => [Hydrator\ReflectionHydrator::class];
        yield 'Reflection'                      => [Hydrator\ReflectionHydrator::class];
    }

    /**
     * @dataProvider hydratorsWithoutConstructors
     */
    public function testInstantiationInitializesFactoriesForHydratorsWithoutConstructorArguments(string $class): void
    {
        $factories = $this->reflectProperty($this->manager, 'factories');

        self::assertArrayHasKey($class, $factories);
        self::assertInstanceOf(Closure::class, $factories[$class]);
    }

    public function testDelegatingHydratorFactoryIsInitialized(): void
    {
        $factories = $this->reflectProperty($this->manager, 'factories');
        self::assertInstanceOf(
            Hydrator\DelegatingHydratorFactory::class,
            $factories[Hydrator\DelegatingHydrator::class]
        );
    }

    public function testHasReturnsFalseForUnknownNames(): void
    {
        self::assertFalse($this->manager->has('unknown-service-name'));
    }

    /** @return Generator<string, array{0: string, 1: class-string}> */
    public function knownServices(): Generator
    {
        foreach ($this->hydratorsWithoutConstructors() as $key => $data) {
            $class = array_pop($data);
            $alias = sprintf('%s alias', $key);
            $fqcn  = sprintf('%s class', $key);

            yield $alias => [$key, $class];
            yield $fqcn  => [$class, $class];
        }

        yield 'DelegatingHydrator alias' => ['DelegatingHydrator', Hydrator\DelegatingHydrator::class];
        yield 'DelegatingHydrator class' => [Hydrator\DelegatingHydrator::class, Hydrator\DelegatingHydrator::class];
    }

    /**
     * @dataProvider knownServices
     */
    public function testHasReturnsTrueForKnownServices(string $service): void
    {
        self::assertTrue($this->manager->has($service));
    }

    public function testGetRaisesExceptionForUnknownService(): void
    {
        $this->expectException(Hydrator\Exception\MissingHydratorServiceException::class);
        $this->manager->get('unknown-service-name');
    }

    /**
     * @dataProvider knownServices
     * @param string|class-string $service
     * @param class-string $expectedType
     */
    public function testGetReturnsExpectedTypesForKnownServices(string $service, string $expectedType): void
    {
        $instance = $this->manager->get($service);
        self::assertInstanceOf($expectedType, $instance);
    }
}
