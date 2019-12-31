<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\Filter;

use Laminas\Hydrator\Filter\NumberOfParameterFilter;

/**
 * Unit tests for {@see NumberOfParameterFilter}
 *
 * @covers \Laminas\Hydrator\Filter\NumberOfParameterFilter
 */
class NumberOfParameterFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group 6083
     */
    public function testArityZero()
    {
        $filter = new NumberOfParameterFilter();
        $this->assertTrue($filter->filter(__CLASS__ . '::methodWithNoParameters'));
        $this->assertFalse($filter->filter(__CLASS__ . '::methodWithOptionalParameters'));
    }

    /**
     * @group 6083
     */
    public function testArityOne()
    {
        $filter = new NumberOfParameterFilter(1);
        $this->assertFalse($filter->filter(__CLASS__ . '::methodWithNoParameters'));
        $this->assertTrue($filter->filter(__CLASS__ . '::methodWithOptionalParameters'));
    }

    /**
     * Test asset method
     */
    public function methodWithOptionalParameters($parameter = 'foo')
    {
    }

    /**
     * Test asset method
     */
    public function methodWithNoParameters()
    {
    }
}
