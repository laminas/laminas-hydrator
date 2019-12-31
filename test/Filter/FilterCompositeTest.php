<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\Filter;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\Filter\GetFilter;
use Laminas\Hydrator\Filter\HasFilter;
use Laminas\Hydrator\Filter\IsFilter;
use Laminas\Hydrator\Filter\NumberOfParameterFilter;

/**
 * Unit tests for {@see FilterComposite}
 *
 * @covers \Laminas\Hydrator\Filter\FilterComposite
 */
class FilterCompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getDataProvider
     */
    public function testFilters($orFilters, $andFilters, $exceptionThrown)
    {
        if ($exceptionThrown) {
            if (empty($orFilters)) {
                $key = 'bar';
            } else {
                $key = 'foo';
            }

            $this->setExpectedException(
                InvalidArgumentException::class,
                sprintf(
                    'The value of %s should be either a callable or an ' .
                    'instance of Laminas\Hydrator\Filter\FilterInterface',
                    $key
                )
            );
        }

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
                ['foo' => 'bar'],
                [],
                'exception' => true
            ],
            [
                [],
                ['bar' => 'foo'],
                'exception' => true
            ],
            [
                ['foo' => ''],
                ['bar' => ''],
                'exception' => true
            ],
            [
                ['foo' => (new HasFilter())],
                ['bar' => (new GetFilter())],
                'exception' => false
            ],
            [
                [
                    'foo1' => (new HasFilter()),
                    'foo2' => (new IsFilter()),
                ],
                [
                    'bar1' => (new GetFilter()),
                    'bar2' => (new NumberOfParameterFilter())
                ],
                'exception' => false
            ]
        ];
    }
}
