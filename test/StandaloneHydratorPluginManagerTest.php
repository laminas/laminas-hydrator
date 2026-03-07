<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Closure;
use Generator;
use Laminas\Hydrator;
use Laminas\Hydrator\StandaloneHydratorPluginManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

use function array_pop;
use function sprintf;

#[CoversClass(StandaloneHydratorPluginManager::class)]
final class StandaloneHydratorPluginManagerTest extends TestCase
{
    private StandaloneHydratorPluginManager $manager;

    protected function setUp(): void
    {
        $this->manager = new StandaloneHydratorPluginManager();
    }

    public function reflectProperty(object $class, string $property): mixed
    {
        $r = new ReflectionProperty($class, $property);
        return $r->getValue($class);
    }

    /**
     * @psalm-return Generator<string, array{0: class-string}>
     */
    public static function hydratorsWithoutConstructors(): Generator
    {
        yield 'ArraySerializable'               => [Hydrator\ArraySerializableHydrator::class];
        yield 'ArraySerializableHydrator'       => [Hydrator\ArraySerializableHydrator::class];
        yield 'ClassMethods'                    => [Hydrator\ClassMethodsHydrator::class];
        yield 'ClassMethodsHydrator'            => [Hydrator\ClassMethodsHydrator::class];
        yield 'ObjectPropertyHydrator'          => [Hydrator\ObjectPropertyHydrator::class];
        yield 'ObjectProperty'                  => [Hydrator\ObjectPropertyHydrator::class];
        yield 'ReflectionHydrator'              => [Hydrator\ReflectionHydrator::class];
        yield 'Reflection'                      => [Hydrator\ReflectionHydrator::class];
    }

    #[DataProvider('hydratorsWithoutConstructors')]
    public function testInstantiationInitializesFactoriesForHydratorsWithoutConstructorArguments(string $class): void
    {
        $factories = $this->reflectProperty($this->manager, 'factories');

        $this->assertArrayHasKey($class, $factories);
        $this->assertInstanceOf(Closure::class, $factories[$class]);
    }

    public function testDelegatingHydratorFactoryIsInitialized(): void
    {
        $factories = $this->reflectProperty($this->manager, 'factories');
        $this->assertInstanceOf(
            Hydrator\DelegatingHydratorFactory::class,
            $factories[Hydrator\DelegatingHydrator::class]
        );
    }

    public function testHasReturnsFalseForUnknownNames(): void
    {
        $this->assertFalse($this->manager->has('unknown-service-name'));
    }

    /** @return Generator<string, array{0: string, 1: class-string}> */
    public static function knownServices(): Generator
    {
        foreach (self::hydratorsWithoutConstructors() as $key => $data) {
            $class = array_pop($data);
            $alias = sprintf('%s alias', $key);
            $fqcn  = sprintf('%s class', $key);

            yield $alias => [$key, $class];
            yield $fqcn  => [$class, $class];
        }

        yield 'DelegatingHydrator alias' => ['DelegatingHydrator', Hydrator\DelegatingHydrator::class];
        yield 'DelegatingHydrator class' => [Hydrator\DelegatingHydrator::class, Hydrator\DelegatingHydrator::class];
    }

    #[DataProvider('knownServices')]
    public function testHasReturnsTrueForKnownServices(string $service): void
    {
        $this->assertTrue($this->manager->has($service));
    }

    public function testGetRaisesExceptionForUnknownService(): void
    {
        $this->expectException(Hydrator\Exception\MissingHydratorServiceException::class);
        $this->manager->get('unknown-service-name');
    }

    /**
     * @param string|class-string $service
     * @param class-string $expectedType
     */
    #[DataProvider('knownServices')]
    public function testGetReturnsExpectedTypesForKnownServices(string $service, string $expectedType): void
    {
        $instance = $this->manager->get($service);
        $this->assertInstanceOf($expectedType, $instance);
    }
}
