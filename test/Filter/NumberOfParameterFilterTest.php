<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\Filter;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Filter\NumberOfParameterFilter;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see NumberOfParameterFilter}
 *
 * @covers \Laminas\Hydrator\Filter\NumberOfParameterFilter
 */
class NumberOfParameterFilterTest extends TestCase
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
     * Verifies an InvalidArgumentException is thrown for a method that doesn't exist
     */
    public function testFilterPropertyDoesNotExist()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Method LaminasTest\Hydrator\Filter\NumberOfParameterFilterTest::methodDoesNotExist doesn\'t exist'
        );
        $filter = new NumberOfParameterFilter(1);
        $filter->filter(__CLASS__ . '::methodDoesNotExist');
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
