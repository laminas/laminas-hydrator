<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Filter;

use Generator;
use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\Filter\FilterInterface;
use Laminas\Hydrator\Filter\GetFilter;
use Laminas\Hydrator\Filter\HasFilter;
use Laminas\Hydrator\Filter\IsFilter;
use Laminas\Hydrator\Filter\NumberOfParameterFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function sprintf;

#[CoversClass(FilterComposite::class)]
class FilterCompositeTest extends TestCase
{
    #[DataProvider('validFiltersProvider')]
    public function testFilters(array $orFilters, array $andFilters): void
    {
        $filter = new FilterComposite($orFilters, $andFilters);

        foreach (array_keys($orFilters) as $name) {
            $this->assertTrue($filter->hasFilter($name));
        }
    }

    /** @return list<array{0: array, 1: array}> */
    public static function validFiltersProvider(): array
    {
        return [
            [
                ['foo' => new HasFilter()],
                ['bar' => new GetFilter()],
            ],
            [
                [
                    'foo1' => new HasFilter(),
                    'foo2' => new IsFilter(),
                ],
                [
                    'bar1' => new GetFilter(),
                    'bar2' => new NumberOfParameterFilter(),
                ],
            ],
        ];
    }

    /** @return list<array{0: array, 1: array, 2: string}> */
    public static function invalidFiltersProvider(): array
    {
        $callback = static fn(): bool => true;

        return [
            [
                ['foo' => 'bar'],
                [],
                'foo',
            ],
            [
                [],
                ['bar' => 'foo'],
                'bar',
            ],
            [
                ['foo' => ''],
                ['bar' => ''],
                'foo',
            ],
            [
                ['foo' => $callback],
                ['bar' => ''],
                'bar',
            ],
            [
                ['foo' => ''],
                ['bar' => $callback],
                'foo',
            ],
        ];
    }

    #[DataProvider('invalidFiltersProvider')]
    public function testConstructWithInvalidFilter(array $orFilters, array $andFilters, string $expectedKey): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'The value of %s should be either a callable or an instance of %s',
            $expectedKey,
            FilterInterface::class
        ));

        new FilterComposite($orFilters, $andFilters);
    }

    public function testNoFilters(): void
    {
        $filter = new FilterComposite();
        self::assertTrue($filter->filter('any_value'));
    }

    /**
     * @param array $values
     * @return list<FilterInterface>
     */
    private static function buildFilters(array $values): array
    {
        $filters = [];
        foreach ($values as $value) {
            $filters[] = new class ($value) implements FilterInterface
            {
                public function __construct(public mixed $value)
                {
                }

                public function filter(string $property, ?object $instance = null): bool
                {
                    return (bool) $this->value;
                }
            };
        }
        return $filters;
    }

    /**
     * @psalm-return Generator<int, array{
     *     orFilters: list<FilterInterface>,
     *     andFilters: list<FilterInterface>,
     *     expected: bool,
     * }, mixed, void>
     */
    private static function generateFilters(
        array $orCompositionFilters,
        array $andCompositionFilters,
        bool $expected
    ): Generator {
        foreach ($orCompositionFilters as $orFilters) {
            foreach ($andCompositionFilters as $andFilters) {
                yield [
                    'orFilters'  => self::buildFilters($orFilters), // boolean sum : true
                    'andFilters' => self::buildFilters($andFilters),
                    'expected'   => $expected,
                ];
            }
        }
    }

    /**
     * @psalm-return Generator<int, array{orFilters: array, andFilters: array, expected: bool}, mixed, void>
     */
    public static function providerCompositionFiltering(): Generator
    {
        $orCompositionFilters = [
            'truthy' => [
                [],
                [true],
                [true, false],
                [true, true],
                [false, true],
            ],
            'falsy'  => [
                [false],
                [false, false],
            ],
        ];

        $andCompositionFilters = [
            'truthy' => [
                [],
                [true],
                [true, true],
            ],
            'falsy'  => [
                [false],
                [true, false],
                [false, true],
                [false, false],
            ],
        ];

        yield from self::generateFilters(
            $orCompositionFilters['truthy'],
            $andCompositionFilters['truthy'],
            true
        );
        yield from self::generateFilters(
            $orCompositionFilters['truthy'],
            $andCompositionFilters['falsy'],
            false
        );
        yield from self::generateFilters(
            $orCompositionFilters['falsy'],
            $andCompositionFilters['truthy'],
            false
        );
        yield from self::generateFilters(
            $orCompositionFilters['falsy'],
            $andCompositionFilters['falsy'],
            false
        );
    }

    /**
     * @param list<FilterInterface> $orFilters
     * @param list<FilterInterface> $andFilters
     */
    #[DataProvider('providerCompositionFiltering')]
    public function testCompositionFiltering(array $orFilters, array $andFilters, bool $expected): void
    {
        $filter = new FilterComposite($orFilters, $andFilters);
        self::assertSame($expected, $filter->filter('any_value'));
    }
}
