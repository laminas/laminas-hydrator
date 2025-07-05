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

class HydratorTest extends TestCase
{
    /** @var ClassMethodsCamelCase */
    protected $classMethodsCamelCase;

    /** @var ClassMethodsTitleCase */
    protected $classMethodsTitleCase;

    /** @var ClassMethodsCamelCaseMissing */
    protected $classMethodsCamelCaseMissing;

    /** @var ClassMethodsUnderscore */
    protected $classMethodsUnderscore;

    /** @var ClassMethodsInvalidParameter */
    protected $classMethodsInvalidParameter;

    /** @var ReflectionAsset */
    protected $reflection;

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
        self::assertSame('1', $this->classMethodsCamelCase->getFooBar());
        self::assertSame('2', $this->classMethodsCamelCase->getFooBarBaz());
        self::assertSame(true, $this->classMethodsCamelCase->getIsFoo());
        self::assertSame(true, $this->classMethodsCamelCase->isBar());
        self::assertSame(true, $this->classMethodsCamelCase->getHasFoo());
        self::assertSame(true, $this->classMethodsCamelCase->hasBar());

        self::assertSame('1', $this->classMethodsTitleCase->getFooBar());
        self::assertSame('2', $this->classMethodsTitleCase->getFooBarBaz());
        self::assertSame(true, $this->classMethodsTitleCase->getIsFoo());
        self::assertSame(true, $this->classMethodsTitleCase->getIsBar());
        self::assertSame(true, $this->classMethodsTitleCase->getHasFoo());
        self::assertSame(true, $this->classMethodsTitleCase->getHasBar());

        self::assertSame('1', $this->classMethodsUnderscore->getFooBar());
        self::assertSame('2', $this->classMethodsUnderscore->getFooBarBaz());
        self::assertSame(true, $this->classMethodsUnderscore->getIsFoo());
        self::assertSame(true, $this->classMethodsUnderscore->isBar());
        self::assertSame(true, $this->classMethodsUnderscore->getHasFoo());
        self::assertSame(true, $this->classMethodsUnderscore->hasBar());
    }

    public function testHydratorReflection(): void
    {
        $hydrator = new ReflectionHydrator();
        $datas    = $hydrator->extract($this->reflection);
        self::assertArrayHaskey('foo', $datas);
        self::assertSame('1', $datas['foo']);
        self::assertArrayHaskey('fooBar', $datas);
        self::assertSame('2', $datas['fooBar']);
        self::assertArrayHaskey('fooBarBaz', $datas);
        self::assertSame('3', $datas['fooBarBaz']);

        $test = $hydrator->hydrate(['foo' => 'foo', 'fooBar' => 'bar', 'fooBarBaz' => 'baz'], $this->reflection);
        self::assertSame('foo', $test->foo);
        self::assertSame('bar', $test->getFooBar());
        self::assertSame('baz', $test->getFooBarBaz());
    }

    public function testHydratorClassMethodsCamelCase(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $datas    = $hydrator->extract($this->classMethodsCamelCase);
        self::assertArrayHaskey('fooBar', $datas);
        self::assertSame('1', $datas['fooBar']);
        self::assertArrayHaskey('fooBarBaz', $datas);
        self::assertArrayNotHasKey('foo_bar', $datas);
        self::assertArrayHaskey('isFoo', $datas);
        self::assertSame(true, $datas['isFoo']);
        self::assertArrayHaskey('isBar', $datas);
        self::assertSame(true, $datas['isBar']);
        self::assertArrayHaskey('hasFoo', $datas);
        self::assertSame(true, $datas['hasFoo']);
        self::assertArrayHaskey('hasBar', $datas);
        self::assertSame(true, $datas['hasBar']);
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
        self::assertSame($this->classMethodsCamelCase, $test);
        self::assertSame('foo', $test->getFooBar());
        self::assertSame('bar', $test->getFooBarBaz());
        self::assertSame(false, $test->getIsFoo());
        self::assertSame(false, $test->isBar());
        self::assertSame(false, $test->getHasFoo());
        self::assertSame(false, $test->hasBar());
    }

    public function testHydratorClassMethodsTitleCase(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $datas    = $hydrator->extract($this->classMethodsTitleCase);
        self::assertArrayHaskey('FooBar', $datas);
        self::assertSame('1', $datas['FooBar']);
        self::assertArrayHaskey('FooBarBaz', $datas);
        self::assertArrayNotHasKey('foo_bar', $datas);
        self::assertArrayHaskey('IsFoo', $datas);
        self::assertSame(true, $datas['IsFoo']);
        self::assertArrayHaskey('IsBar', $datas);
        self::assertSame(true, $datas['IsBar']);
        self::assertArrayHaskey('HasFoo', $datas);
        self::assertSame(true, $datas['HasFoo']);
        self::assertArrayHaskey('HasBar', $datas);
        self::assertSame(true, $datas['HasBar']);
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
        self::assertSame($this->classMethodsTitleCase, $test);
        self::assertSame('foo', $test->getFooBar());
        self::assertSame('bar', $test->getFooBarBaz());
        self::assertSame(false, $test->getIsFoo());
        self::assertSame(false, $test->getIsBar());
        self::assertSame(false, $test->getHasFoo());
        self::assertSame(false, $test->getHasBar());
    }

    public function testHydratorClassMethodsUnderscore(): void
    {
        $hydrator = new ClassMethodsHydrator(true);
        $datas    = $hydrator->extract($this->classMethodsUnderscore);
        self::assertArrayHaskey('foo_bar', $datas);
        self::assertSame('1', $datas['foo_bar']);
        self::assertArrayHaskey('foo_bar_baz', $datas);
        self::assertArrayNotHasKey('fooBar', $datas);
        self::assertArrayHaskey('is_foo', $datas);
        self::assertArrayNotHasKey('isFoo', $datas);
        self::assertSame(true, $datas['is_foo']);
        self::assertArrayHaskey('is_bar', $datas);
        self::assertArrayNotHasKey('isBar', $datas);
        self::assertSame(true, $datas['is_bar']);
        self::assertArrayHaskey('has_foo', $datas);
        self::assertArrayNotHasKey('hasFoo', $datas);
        self::assertSame(true, $datas['has_foo']);
        self::assertArrayHaskey('has_bar', $datas);
        self::assertArrayNotHasKey('hasBar', $datas);
        self::assertSame(true, $datas['has_bar']);
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
        self::assertSame($this->classMethodsUnderscore, $test);
        self::assertSame('foo', $test->getFooBar());
        self::assertSame('bar', $test->getFooBarBaz());
        self::assertSame(false, $test->getIsFoo());
        self::assertSame(false, $test->isBar());
        self::assertSame(false, $test->getHasFoo());
        self::assertSame(false, $test->hasBar());
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
        self::assertSame($this->classMethodsUnderscore, $test);
        self::assertSame('foo', $test->getFooBar());
        self::assertSame('bar', $test->getFooBarBaz());
        self::assertSame(false, $test->getIsFoo());
        self::assertSame(false, $test->isBar());
        self::assertSame(false, $test->getHasFoo());
        self::assertSame(false, $test->hasBar());
    }

    public function testHydratorClassMethodsOptions(): void
    {
        $hydrator = new ClassMethodsHydrator();
        self::assertTrue($hydrator->getUnderscoreSeparatedKeys());
        $hydrator->setOptions(['underscoreSeparatedKeys' => false]);
        self::assertFalse($hydrator->getUnderscoreSeparatedKeys());
        $hydrator->setUnderscoreSeparatedKeys(true);
        self::assertTrue($hydrator->getUnderscoreSeparatedKeys());
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
        self::assertSame($this->classMethodsUnderscore, $test);
    }

    public function testHydratorClassMethodsDefaultBehaviorIsConvertUnderscoreToCamelCase(): void
    {
        $hydrator = new ClassMethodsHydrator();
        $datas    = $hydrator->extract($this->classMethodsUnderscore);
        self::assertArrayHaskey('foo_bar', $datas);
        self::assertSame('1', $datas['foo_bar']);
        self::assertArrayHaskey('foo_bar_baz', $datas);
        self::assertArrayNotHaskey('fooBar', $datas);
        $test = $hydrator->hydrate(['foo_bar' => 'foo', 'foo_bar_baz' => 'bar'], $this->classMethodsUnderscore);
        self::assertSame($this->classMethodsUnderscore, $test);
        self::assertSame('foo', $test->getFooBar());
        self::assertSame('bar', $test->getFooBarBaz());
    }

    public function testRetrieveWildStrategyAndOther(): void
    {
        $hydrator = new ClassMethodsHydrator();
        $hydrator->addStrategy('default', new DefaultStrategy());
        $hydrator->addStrategy('*', new SerializableStrategy(new PhpSerialize()));
        $default = $hydrator->getStrategy('default');
        self::assertInstanceOf(DefaultStrategy::class, $default);
        $serializable = $hydrator->getStrategy('*');
        self::assertInstanceOf(SerializableStrategy::class, $serializable);
    }

    public function testUseWildStrategyByDefault(): void
    {
        $hydrator = new ClassMethodsHydrator();
        $datas    = $hydrator->extract($this->classMethodsUnderscore);

        self::assertSame('1', $datas['foo_bar']);

        $hydrator->addStrategy('*', new SerializableStrategy(new PhpSerialize()));
        $datas = $hydrator->extract($this->classMethodsUnderscore);

        self::assertSame('s:1:"1";', $datas['foo_bar']);
    }

    public function testUseWildStrategyAndOther(): void
    {
        $hydrator = new ClassMethodsHydrator();
        $datas    = $hydrator->extract($this->classMethodsUnderscore);
        self::assertSame('1', $datas['foo_bar']);

        $hydrator->addStrategy('foo_bar', new DefaultStrategy());
        $hydrator->addStrategy('*', new SerializableStrategy(new PhpSerialize()));
        $datas = $hydrator->extract($this->classMethodsUnderscore);
        self::assertSame('1', $datas['foo_bar']);
        self::assertSame('s:1:"2";', $datas['foo_bar_baz']);
    }

    public function testHydratorClassMethodsCamelCaseWithSetterMissing(): void
    {
        $hydrator = new ClassMethodsHydrator(false);

        $datas = $hydrator->extract($this->classMethodsCamelCaseMissing);
        self::assertArrayHaskey('fooBar', $datas);
        self::assertSame('1', $datas['fooBar']);
        self::assertArrayHaskey('fooBarBaz', $datas);
        self::assertArrayNotHaskey('foo_bar', $datas);
        $test = $hydrator->hydrate(['fooBar' => 'foo', 'fooBarBaz' => 1], $this->classMethodsCamelCaseMissing);
        self::assertSame($this->classMethodsCamelCaseMissing, $test);
        self::assertSame('foo', $test->getFooBar());
        self::assertSame('2', $test->getFooBarBaz());
    }

    public function testHydratorClassMethodsManipulateFilter(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $datas    = $hydrator->extract($this->classMethodsCamelCase);

        self::assertArrayHaskey('fooBar', $datas);
        self::assertSame('1', $datas['fooBar']);
        self::assertArrayHaskey('fooBarBaz', $datas);
        self::assertArrayNotHasKey('foo_bar', $datas);
        self::assertArrayHaskey('isFoo', $datas);
        self::assertSame(true, $datas['isFoo']);
        self::assertArrayHaskey('isBar', $datas);
        self::assertSame(true, $datas['isBar']);
        self::assertArrayHaskey('hasFoo', $datas);
        self::assertSame(true, $datas['hasFoo']);
        self::assertArrayHaskey('hasBar', $datas);
        self::assertSame(true, $datas['hasBar']);

        $hydrator->removeFilter('has');
        $datas = $hydrator->extract($this->classMethodsCamelCase);
        self::assertArrayHaskey('hasFoo', $datas); //method is getHasFoo
        self::assertArrayNotHaskey('hasBar', $datas); //method is hasBar
    }

    public function testHydratorClassMethodsWithCustomFilter(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $hydrator->extract($this->classMethodsCamelCase);
        $hydrator->addFilter(
            'exclude',
            static function ($property): bool {
                $method = explode('::', $property)[1];

                if ($method === 'getHasFoo') {
                    return false;
                }

                return true;
            },
            FilterComposite::CONDITION_AND
        );

        $datas = $hydrator->extract($this->classMethodsCamelCase);
        self::assertArrayNotHaskey('hasFoo', $datas);
    }

    #[DataProvider('filterProvider')]
    public function testArraySerializableFilter(
        AbstractHydrator $hydrator,
        object $serializable
    ): void {
        self::assertSame(
            [
                'foo'   => 'bar',
                'bar'   => 'foo',
                'blubb' => 'baz',
                'quo'   => 'blubb',
            ],
            $hydrator->extract($serializable)
        );

        $hydrator->addFilter('foo', static function ($property): bool {
            if ($property === 'foo') {
                return false;
            }
            return true;
        });

        self::assertSame(
            [
                'bar'   => 'foo',
                'blubb' => 'baz',
                'quo'   => 'blubb',
            ],
            $hydrator->extract($serializable)
        );

        $hydrator->addFilter('len', static function ($property): bool {
            if (strlen($property) !== 3) {
                return false;
            }
            return true;
        }, FilterComposite::CONDITION_AND);

        self::assertSame(
            [
                'bar' => 'foo',
                'quo' => 'blubb',
            ],
            $hydrator->extract($serializable)
        );

        $hydrator->removeFilter('len');
        $hydrator->removeFilter('foo');

        self::assertSame(
            [
                'foo'   => 'bar',
                'bar'   => 'foo',
                'blubb' => 'baz',
                'quo'   => 'blubb',
            ],
            $hydrator->extract($serializable)
        );
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

        self::assertTrue($datas['hasBar']);
        self::assertSame('Bar', $datas['foo']);
        self::assertFalse($datas['isBla']);
    }

    public function testObjectBasedFilters(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $foo      = new ClassMethodsFilterProviderInterface();
        $data     = $hydrator->extract($foo);
        self::assertArrayNotHasKey('filter', $data);
        self::assertSame('bar', $data['foo']);
        self::assertSame('foo', $data['bar']);
    }

    public function testHydratorClassMethodsWithProtectedSetter(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $object   = new ClassMethodsProtectedSetter();
        $hydrator->hydrate(['foo' => 'bar', 'bar' => 'BAR'], $object);
        $data = $hydrator->extract($object);

        self::assertSame('BAR', $data['bar']);
    }

    public function testHydratorClassMethodsWithMagicMethodSetter(): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $object   = new ClassMethodsMagicMethodSetter();
        $hydrator->hydrate(['foo' => 'bar'], $object);
        $data = $hydrator->extract($object);

        self::assertSame('bar', $data['foo']);
    }

    public function testHydratorClassMethodsWithMagicMethodSetterAndMethodExistsCheck(): void
    {
        $hydrator = new ClassMethodsHydrator(false, true);
        $object   = new ClassMethodsMagicMethodSetter();
        $hydrator->hydrate(['foo' => 'bar'], $object);
        $data = $hydrator->extract($object);

        self::assertNull($data['foo']);
    }
}
