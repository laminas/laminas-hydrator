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
     * @dataProvider getDataProvider
     */
    public function testFilters($orFilters, $andFilters)
    {
        $filter = new FilterComposite($orFilters, $andFilters);

        foreach ($orFilters as $name => $value) {
            $this->assertTrue($filter->hasFilter($name));
        }
    }

    /**
     * @return array
     */
    public function getDataProvider()
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

    public function providerConstructWithInvalidFilter(): array
    {
        $callback = function () {
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
     * @dataProvider providerConstructWithInvalidFilter
     */
    public function testConstructWithInvalidFilter($orFilters, $andFilters, $expectedKey)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The value of %s should be either a callable or an instance of %s',
                $expectedKey,
                'Laminas\Hydrator\Filter\FilterInterface'
            )
        );

        $filter = new FilterComposite($orFilters, $andFilters);
    }
}
