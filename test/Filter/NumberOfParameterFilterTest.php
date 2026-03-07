<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Filter;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Filter\NumberOfParameterFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[CoversClass(NumberOfParameterFilter::class)]
final class NumberOfParameterFilterTest extends TestCase
{
    #[Group('6083')]
    public function testArityZero(): void
    {
        $filter = new NumberOfParameterFilter();
        $this->assertTrue($filter->filter(self::class . '::methodWithNoParameters'));
        $this->assertFalse($filter->filter(self::class . '::methodWithOptionalParameters'));
    }

    /**
     * Test asset method.
     *
     * @psalm-suppress UnusedParam
     */
    public function methodWithOptionalParameters(string $parameter = 'foo'): void
    {
    }

    #[Group('6083')]
    public function testArityOne(): void
    {
        $filter = new NumberOfParameterFilter(1);
        $this->assertFalse($filter->filter(self::class . '::methodWithNoParameters'));
        $this->assertTrue($filter->filter(self::class . '::methodWithOptionalParameters'));
    }

    /**
     * Verifies an InvalidArgumentException is thrown for a method that doesn't exist
     */
    public function testFilterPropertyDoesNotExist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Method LaminasTest\Hydrator\Filter\NumberOfParameterFilterTest::methodDoesNotExist does not exist'
        );
        $filter = new NumberOfParameterFilter(1);
        $filter->filter(self::class . '::methodDoesNotExist');
    }

    /**
     * Test asset method
     */
    public function methodWithNoParameters(): void
    {
    }
}
