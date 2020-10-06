<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\Filter;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\Filter\FilterInterface;
use Laminas\Hydrator\Filter\GetFilter;
use Laminas\Hydrator\Filter\HasFilter;
use Laminas\Hydrator\Filter\IsFilter;
use Laminas\Hydrator\Filter\NumberOfParameterFilter;
use PHPUnit\Framework\TestCase;

use function sprintf;

/**
 * Unit tests for {@see FilterComposite}
 *
 * @covers \Laminas\Hydrator\Filter\FilterComposite
 */
class FilterCompositeTest extends TestCase
{
    /**
     * @dataProvider validFiltersProvider
     *
     * @return void
     */
    public function testFilters(array $orFilters, array $andFilters): void
    {
        $filter = new FilterComposite($orFilters, $andFilters);

        foreach ($orFilters as $name => $value) {
            $this->assertTrue($filter->hasFilter($name));
        }
    }

    public function validFiltersProvider() : array
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

    public function invalidFiltersProvider() : array
    {
        $callback = /**
         * @return true
         */
        static function (): bool {
            return true;
        };

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

    /**
     * @dataProvider invalidFiltersProvider
     *
     * @return void
     */
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

    private function buildFilters(array $values) : array
    {
        $filters = [];
        foreach ($values as $value) {
            $filters[] = new class($value) implements FilterInterface
            {
                public function __construct($value)
                {
                    $this->value = $value;
                }
                public function filter(string $property, ?object $instance = null) : bool
                {
                    return $this->value;
                }
            };
        }
        return $filters;
    }

    /**
     * @return \Generator
     *
     * @psalm-return \Generator<int, array{orFilters: array, andFilters: array, expected: bool}, mixed, void>
     */
    private function generateFilters(array $orCompositionFilters, array $andCompositionFilters, bool $expected): \Generator
    {
        foreach ($orCompositionFilters as $orFilters) {
            foreach ($andCompositionFilters as $andFilters) {
                yield [
                    'orFilters' => $this->buildFilters($orFilters),  // boolean sum : true
                    'andFilters' => $this->buildFilters($andFilters),
                    'expected' => $expected,
                ];
            }
        }
    }

    /**
     * @return \Generator
     *
     * @psalm-return \Generator<mixed, mixed, mixed, void>
     */
    public function providerCompositionFiltering(): \Generator
    {
        $orCompositionFilters = [
            'truthy' => [
                [],
                [true],
                [true, false],
                [true, true],
                [false, true],
            ],
            'falsy' => [
                [false],
                [false, false],
            ]
        ];

        $andCompositionFilters = [
            'truthy' => [
                [],
                [true],
                [true, true],
            ],
            'falsy' => [
                [false],
                [true, false],
                [false, true],
                [false, false],
            ]
        ];

        yield from $this->generateFilters(
            $orCompositionFilters['truthy'],
            $andCompositionFilters['truthy'],
            true
        );
        yield from $this->generateFilters(
            $orCompositionFilters['truthy'],
            $andCompositionFilters['falsy'],
            false
        );
        yield from $this->generateFilters(
            $orCompositionFilters['falsy'],
            $andCompositionFilters['truthy'],
            false
        );
        yield from $this->generateFilters(
            $orCompositionFilters['falsy'],
            $andCompositionFilters['falsy'],
            false
        );
    }

    /**
     * @dataProvider providerCompositionFiltering
     *
     * @return void
     */
    public function testCompositionFiltering(array $orFilters, array $andFilters, bool $expected): void
    {
        $filter = new FilterComposite($orFilters, $andFilters);
        self::assertSame($expected, $filter->filter('any_value'));
    }
}
