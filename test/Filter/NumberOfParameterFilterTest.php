<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

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
     *
     * @return void
     */
    public function testArityZero(): void
    {
        $filter = new NumberOfParameterFilter();
        $this->assertTrue($filter->filter(__CLASS__ . '::methodWithNoParameters'));
        $this->assertFalse($filter->filter(__CLASS__ . '::methodWithOptionalParameters'));
    }

    /**
     * @group 6083
     *
     * @return void
     */
    public function testArityOne(): void
    {
        $filter = new NumberOfParameterFilter(1);
        $this->assertFalse($filter->filter(__CLASS__ . '::methodWithNoParameters'));
        $this->assertTrue($filter->filter(__CLASS__ . '::methodWithOptionalParameters'));
    }

    /**
     * Verifies an InvalidArgumentException is thrown for a method that doesn't exist
     *
     * @return void
     */
    public function testFilterPropertyDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Method LaminasTest\Hydrator\Filter\NumberOfParameterFilterTest::methodDoesNotExist does not exist'
        );
        $filter = new NumberOfParameterFilter(1);
        $filter->filter(__CLASS__ . '::methodDoesNotExist');
    }

    /**
     * Test asset method
     *
     * @return void
     */
    public function methodWithOptionalParameters($parameter = 'foo'): void
    {
    }

    /**
     * Test asset method
     *
     * @return void
     */
    public function methodWithNoParameters(): void
    {
    }
}
