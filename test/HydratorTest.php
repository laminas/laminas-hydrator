<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Hydrator\Strategy\DefaultStrategy;
use Laminas\Hydrator\Strategy\SerializableStrategy;
use Laminas\Serializer\Adapter\PhpSerialize;
use LaminasTest\Hydrator\TestAsset\ArraySerializable as ArraySerializableAsset;
use LaminasTest\Hydrator\TestAsset\ClassMethodsCamelCase;
use LaminasTest\Hydrator\TestAsset\ClassMethodsCamelCaseMissing;
use LaminasTest\Hydrator\TestAsset\ClassMethodsFilterProviderInterface;
use LaminasTest\Hydrator\TestAsset\ClassMethodsInvalidParameter;
use LaminasTest\Hydrator\TestAsset\ClassMethodsMagicMethodSetter;
use LaminasTest\Hydrator\TestAsset\ClassMethodsProtectedSetter;
use LaminasTest\Hydrator\TestAsset\ClassMethodsTitleCase;
use LaminasTest\Hydrator\TestAsset\ClassMethodsUnderscore;
use LaminasTest\Hydrator\TestAsset\ObjectProperty as ObjectPropertyAsset;
use LaminasTest\Hydrator\TestAsset\Reflection as ReflectionAsset;
use LaminasTest\Hydrator\TestAsset\ReflectionFilter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function explode;
use function strlen;

final class HydratorTest extends TestCase
{
    private ClassMethodsCamelCase $classMethodsCamelCase;

    private ClassMethodsTitleCase $classMethodsTitleCase;

    private ClassMethodsCamelCaseMissing $classMethodsCamelCaseMissing;

    private ClassMethodsUnderscore $classMethodsUnderscore;

    private ClassMethodsInvalidParameter $classMethodsInvalidParameter;

    private ReflectionAsset $reflection;

    protected function setUp(): void
    {
        $this->classMethodsCamelCase        = new ClassMethodsCamelCase();
        $this->classMethodsTitleCase        = new ClassMethodsTitleCase();
        $this->classMethodsCamelCaseMissing = new ClassMethodsCamelCaseMissing();
        $this->classMethodsUnderscore       = new ClassMethodsUnderscore();
        $this->classMethodsInvalidParameter = new ClassMethodsInvalidParameter();
        $this->reflection                   = new ReflectionAsset();
        $this->classMethodsInvalidParameter = new ClassMethodsInvalidParameter();
    }

    public function testInitiateValues(): void
    {
        $this->assertSame('1', $this->classMethodsCamelCase->getFooBar());
        $this->assertSame('2', $this->classMethodsCamelCase->getFooBarBaz());
        $this->assertTrue($this->classMethodsCamelCase->getIsFoo());
        $this->assertTrue($this->classMethodsCamelCase->isBar());
        $this->assertTrue($this->classMethodsCamelCase->getHasFoo());
        $this->assertTrue($this->classMethodsCamelCase->hasBar());

        $this->assertSame('1', $this->classMethodsTitleCase->getFooBar());
        $this->assertSame('2', $this->classMethodsTitleCase->getFooBarBaz());
        $this->assertTrue($this->classMethodsTitleCase->getIsFoo());
        $this->assertTrue($this->classMethodsTitleCase->getIsBar());
        $this->assertTrue($this->classMethodsTitleCase->getHasFoo());
        $this->assertTrue($this->classMethodsTitleCase->getHasBar());

        $this->assertSame('1', $this->classMethodsUnderscore->getFooBar());
        $this->assertSame('2', $this->classMethodsUnderscore->getFooBarBaz());
        $this->assertTrue($this->classMethodsUnderscore->getIsFoo());
        $this->assertTrue($this->classMethodsUnderscore->isBar());
        $this->assertTrue($this->classMethodsUnderscore->getHasFoo());
        $this->assertTrue($this->classMethodsUnderscore->hasBar());
    }

    public function testHydratorReflection(): void
    {
        $hydrator = new ReflectionHydrator();
        $datas    = $hydrator->extract($this->reflection);
        $this->assertArrayHaskey('foo', $datas);
        $this->assertSame('1', $datas['foo']);
        $this->assertArrayHaskey('fooBar', $datas);
        $this->assertSame('2', $datas['fooBar']);
        $this->assertArrayHaskey('fooBarBaz', $datas);
        $this->assertSame('3', $datas['fooBarBaz']);

        $test = $hydrator->hydrate(['foo' => 'foo', 'fooBar' => 'bar', 'fooBarBaz' => 'baz'], $this->reflection);
        $this->assertSame('foo', $test->foo);
        $this->assertSame('bar', $test->getFooBar());
        $this->assertSame('baz', $test->getFooBarBaz());
    }

    public function testHydratorClassMethodsCamelCase(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $datas    = $hydrator->extract($this->classMethodsCamelCase);
        $this->assertArrayHaskey('fooBar', $datas);
        $this->assertSame('1', $datas['fooBar']);
        $this->assertArrayHaskey('fooBarBaz', $datas);
        $this->assertArrayNotHasKey('foo_bar', $datas);
        $this->assertArrayHaskey('isFoo', $datas);
        $this->assertTrue($datas['isFoo']);
        $this->assertArrayHaskey('isBar', $datas);
        $this->assertTrue($datas['isBar']);
        $this->assertArrayHaskey('hasFoo', $datas);
        $this->assertTrue($datas['hasFoo']);
        $this->assertArrayHaskey('hasBar', $datas);
        $this->assertTrue($datas['hasBar']);
        $test = $hydrator->hydrate(
            [
                'fooBar'    => 'foo',
                'fooBarBaz' => 'bar',
                'isFoo'     => false,
                'isBar'     => false,
                'hasFoo'    => false,
                'hasBar'    => false,
            ],
            $this->classMethodsCamelCase
        );
        $this->assertSame($this->classMethodsCamelCase, $test);
        $this->assertSame('foo', $test->getFooBar());
        $this->assertSame('bar', $test->getFooBarBaz());
        $this->assertFalse($test->getIsFoo());
        $this->assertFalse($test->isBar());
        $this->assertFalse($test->getHasFoo());
        $this->assertFalse($test->hasBar());
    }

    public function testHydratorClassMethodsTitleCase(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $datas    = $hydrator->extract($this->classMethodsTitleCase);
        $this->assertArrayHaskey('FooBar', $datas);
        $this->assertSame('1', $datas['FooBar']);
        $this->assertArrayHaskey('FooBarBaz', $datas);
        $this->assertArrayNotHasKey('foo_bar', $datas);
        $this->assertArrayHaskey('IsFoo', $datas);
        $this->assertTrue($datas['IsFoo']);
        $this->assertArrayHaskey('IsBar', $datas);
        $this->assertTrue($datas['IsBar']);
        $this->assertArrayHaskey('HasFoo', $datas);
        $this->assertTrue($datas['HasFoo']);
        $this->assertArrayHaskey('HasBar', $datas);
        $this->assertTrue($datas['HasBar']);
        $test = $hydrator->hydrate(
            [
                'FooBar'    => 'foo',
                'FooBarBaz' => 'bar',
                'IsFoo'     => false,
                'IsBar'     => false,
                'HasFoo'    => false,
                'HasBar'    => false,
            ],
            $this->classMethodsTitleCase
        );
        $this->assertSame($this->classMethodsTitleCase, $test);
        $this->assertSame('foo', $test->getFooBar());
        $this->assertSame('bar', $test->getFooBarBaz());
        $this->assertFalse($test->getIsFoo());
        $this->assertFalse($test->getIsBar());
        $this->assertFalse($test->getHasFoo());
        $this->assertFalse($test->getHasBar());
    }

    public function testHydratorClassMethodsUnderscore(): void
    {
        $hydrator = new ClassMethodsHydrator(true);
        $datas    = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertArrayHaskey('foo_bar', $datas);
        $this->assertSame('1', $datas['foo_bar']);
        $this->assertArrayHaskey('foo_bar_baz', $datas);
        $this->assertArrayNotHasKey('fooBar', $datas);
        $this->assertArrayHaskey('is_foo', $datas);
        $this->assertArrayNotHasKey('isFoo', $datas);
        $this->assertTrue($datas['is_foo']);
        $this->assertArrayHaskey('is_bar', $datas);
        $this->assertArrayNotHasKey('isBar', $datas);
        $this->assertTrue($datas['is_bar']);
        $this->assertArrayHaskey('has_foo', $datas);
        $this->assertArrayNotHasKey('hasFoo', $datas);
        $this->assertTrue($datas['has_foo']);
        $this->assertArrayHaskey('has_bar', $datas);
        $this->assertArrayNotHasKey('hasBar', $datas);
        $this->assertTrue($datas['has_bar']);
        $test = $hydrator->hydrate(
            [
                'foo_bar'     => 'foo',
                'foo_bar_baz' => 'bar',
                'is_foo'      => false,
                'is_bar'      => false,
                'has_foo'     => false,
                'has_bar'     => false,
            ],
            $this->classMethodsUnderscore
        );
        $this->assertSame($this->classMethodsUnderscore, $test);
        $this->assertSame('foo', $test->getFooBar());
        $this->assertSame('bar', $test->getFooBarBaz());
        $this->assertFalse($test->getIsFoo());
        $this->assertFalse($test->isBar());
        $this->assertFalse($test->getHasFoo());
        $this->assertFalse($test->hasBar());
    }

    public function testHydratorClassMethodsUnderscoreWithUnderscoreUpperCasedHydrateDataKeys(): void
    {
        $hydrator = new ClassMethodsHydrator(true);
        $hydrator->extract($this->classMethodsUnderscore);
        $test = $hydrator->hydrate(
            [
                'FOO_BAR'     => 'foo',
                'FOO_BAR_BAZ' => 'bar',
                'IS_FOO'      => false,
                'IS_BAR'      => false,
                'HAS_FOO'     => false,
                'HAS_BAR'     => false,
            ],
            $this->classMethodsUnderscore
        );
        $this->assertSame($this->classMethodsUnderscore, $test);
        $this->assertSame('foo', $test->getFooBar());
        $this->assertSame('bar', $test->getFooBarBaz());
        $this->assertFalse($test->getIsFoo());
        $this->assertFalse($test->isBar());
        $this->assertFalse($test->getHasFoo());
        $this->assertFalse($test->hasBar());
    }

    public function testHydratorClassMethodsOptions(): void
    {
        $hydrator = new ClassMethodsHydrator();
        $this->assertTrue($hydrator->getUnderscoreSeparatedKeys());
        $hydrator->setOptions(['underscoreSeparatedKeys' => false]);
        $this->assertFalse($hydrator->getUnderscoreSeparatedKeys());
        $hydrator->setUnderscoreSeparatedKeys(true);
        $this->assertTrue($hydrator->getUnderscoreSeparatedKeys());
    }

    public function testHydratorClassMethodsIgnoresInvalidValues(): void
    {
        $hydrator = new ClassMethodsHydrator(true);
        $data     = [
            'foo_bar'     => '1',
            'foo_bar_baz' => '2',
            'invalid'     => 'value',
        ];
        $test     = $hydrator->hydrate($data, $this->classMethodsUnderscore);
        $this->assertSame($this->classMethodsUnderscore, $test);
    }

    public function testHydratorClassMethodsDefaultBehaviorIsConvertUnderscoreToCamelCase(): void
    {
        $hydrator = new ClassMethodsHydrator();
        $datas    = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertArrayHaskey('foo_bar', $datas);
        $this->assertSame('1', $datas['foo_bar']);
        $this->assertArrayHaskey('foo_bar_baz', $datas);
        $this->assertArrayNotHaskey('fooBar', $datas);
        $test = $hydrator->hydrate(['foo_bar' => 'foo', 'foo_bar_baz' => 'bar'], $this->classMethodsUnderscore);
        $this->assertSame($this->classMethodsUnderscore, $test);
        $this->assertSame('foo', $test->getFooBar());
        $this->assertSame('bar', $test->getFooBarBaz());
    }

    public function testRetrieveWildStrategyAndOther(): void
    {
        $hydrator = new ClassMethodsHydrator();
        $hydrator->addStrategy('default', new DefaultStrategy());
        $hydrator->addStrategy('*', new SerializableStrategy(new PhpSerialize()));
        $default = $hydrator->getStrategy('default');
        $this->assertInstanceOf(DefaultStrategy::class, $default);
        $serializable = $hydrator->getStrategy('*');
        $this->assertInstanceOf(SerializableStrategy::class, $serializable);
    }

    public function testUseWildStrategyByDefault(): void
    {
        $hydrator = new ClassMethodsHydrator();
        $datas    = $hydrator->extract($this->classMethodsUnderscore);

        $this->assertSame('1', $datas['foo_bar']);

        $hydrator->addStrategy('*', new SerializableStrategy(new PhpSerialize()));
        $datas = $hydrator->extract($this->classMethodsUnderscore);

        $this->assertSame('s:1:"1";', $datas['foo_bar']);
    }

    public function testUseWildStrategyAndOther(): void
    {
        $hydrator = new ClassMethodsHydrator();
        $datas    = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertSame('1', $datas['foo_bar']);

        $hydrator->addStrategy('foo_bar', new DefaultStrategy());
        $hydrator->addStrategy('*', new SerializableStrategy(new PhpSerialize()));
        $datas = $hydrator->extract($this->classMethodsUnderscore);
        $this->assertSame('1', $datas['foo_bar']);
        $this->assertSame('s:1:"2";', $datas['foo_bar_baz']);
    }

    public function testHydratorClassMethodsCamelCaseWithSetterMissing(): void
    {
        $hydrator = new ClassMethodsHydrator(false);

        $datas = $hydrator->extract($this->classMethodsCamelCaseMissing);
        $this->assertArrayHaskey('fooBar', $datas);
        $this->assertSame('1', $datas['fooBar']);
        $this->assertArrayHaskey('fooBarBaz', $datas);
        $this->assertArrayNotHaskey('foo_bar', $datas);
        $test = $hydrator->hydrate(['fooBar' => 'foo', 'fooBarBaz' => 1], $this->classMethodsCamelCaseMissing);
        $this->assertSame($this->classMethodsCamelCaseMissing, $test);
        $this->assertSame('foo', $test->getFooBar());
        $this->assertSame('2', $test->getFooBarBaz());
    }

    public function testHydratorClassMethodsManipulateFilter(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $datas    = $hydrator->extract($this->classMethodsCamelCase);

        $this->assertArrayHaskey('fooBar', $datas);
        $this->assertSame('1', $datas['fooBar']);
        $this->assertArrayHaskey('fooBarBaz', $datas);
        $this->assertArrayNotHasKey('foo_bar', $datas);
        $this->assertArrayHaskey('isFoo', $datas);
        $this->assertTrue($datas['isFoo']);
        $this->assertArrayHaskey('isBar', $datas);
        $this->assertTrue($datas['isBar']);
        $this->assertArrayHaskey('hasFoo', $datas);
        $this->assertTrue($datas['hasFoo']);
        $this->assertArrayHaskey('hasBar', $datas);
        $this->assertTrue($datas['hasBar']);

        $hydrator->removeFilter('has');
        $datas = $hydrator->extract($this->classMethodsCamelCase);
        $this->assertArrayHaskey('hasFoo', $datas); //method is getHasFoo
        $this->assertArrayNotHaskey('hasBar', $datas); //method is hasBar
    }

    public function testHydratorClassMethodsWithCustomFilter(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $hydrator->extract($this->classMethodsCamelCase);
        $hydrator->addFilter(
            'exclude',
            static function ($property): bool {
                $method = explode('::', $property)[1];
                return $method !== 'getHasFoo';
            },
            FilterComposite::CONDITION_AND
        );

        $datas = $hydrator->extract($this->classMethodsCamelCase);
        $this->assertArrayNotHaskey('hasFoo', $datas);
    }

    #[DataProvider('filterProvider')]
    public function testArraySerializableFilter(
        AbstractHydrator $hydrator,
        object $serializable
    ): void {
        $this->assertSame([
            'foo'   => 'bar',
            'bar'   => 'foo',
            'blubb' => 'baz',
            'quo'   => 'blubb',
        ], $hydrator->extract($serializable));

        $hydrator->addFilter('foo', static fn($property): bool => $property !== 'foo');

        $this->assertSame([
            'bar'   => 'foo',
            'blubb' => 'baz',
            'quo'   => 'blubb',
        ], $hydrator->extract($serializable));

        $hydrator->addFilter(
            'len',
            static fn($property): bool => strlen($property) === 3,
            FilterComposite::CONDITION_AND
        );

        $this->assertSame([
            'bar' => 'foo',
            'quo' => 'blubb',
        ], $hydrator->extract($serializable));

        $hydrator->removeFilter('len');
        $hydrator->removeFilter('foo');

        $this->assertSame([
            'foo'   => 'bar',
            'bar'   => 'foo',
            'blubb' => 'baz',
            'quo'   => 'blubb',
        ], $hydrator->extract($serializable));
    }

    /**
     * @psalm-return list<array{0: AbstractHydrator, 1: object}>
     */
    public static function filterProvider(): array
    {
        return [
            [new ObjectPropertyHydrator(), new ObjectPropertyAsset()],
            [new ArraySerializableHydrator(), new ArraySerializableAsset()],
            [new ReflectionHydrator(), new ReflectionFilter()],
        ];
    }

    public function testHydratorClassMethodsWithInvalidNumberOfParameters(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $datas    = $hydrator->extract($this->classMethodsInvalidParameter);

        $this->assertTrue($datas['hasBar']);
        $this->assertSame('Bar', $datas['foo']);
        $this->assertFalse($datas['isBla']);
    }

    public function testObjectBasedFilters(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $foo      = new ClassMethodsFilterProviderInterface();
        $data     = $hydrator->extract($foo);
        $this->assertArrayNotHasKey('filter', $data);
        $this->assertSame('bar', $data['foo']);
        $this->assertSame('foo', $data['bar']);
    }

    public function testHydratorClassMethodsWithProtectedSetter(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $object   = new ClassMethodsProtectedSetter();
        $hydrator->hydrate(['foo' => 'bar', 'bar' => 'BAR'], $object);
        $data = $hydrator->extract($object);

        $this->assertSame('BAR', $data['bar']);
    }

    public function testHydratorClassMethodsWithMagicMethodSetter(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $object   = new ClassMethodsMagicMethodSetter();
        $hydrator->hydrate(['foo' => 'bar'], $object);
        $data = $hydrator->extract($object);

        $this->assertSame('bar', $data['foo']);
    }

    public function testHydratorClassMethodsWithMagicMethodSetterAndMethodExistsCheck(): void
    {
        $hydrator = new ClassMethodsHydrator(false, true);
        $object   = new ClassMethodsMagicMethodSetter();
        $hydrator->hydrate(['foo' => 'bar'], $object);
        $data = $hydrator->extract($object);

        $this->assertNull($data['foo']);
    }
}
