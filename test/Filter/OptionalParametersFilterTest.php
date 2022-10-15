<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Filter;

use InvalidArgumentException;
use Laminas\Hydrator\Filter\OptionalParametersFilter;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see OptionalParametersFilter}
 *
 * @covers \Laminas\Hydrator\Filter\OptionalParametersFilter
 */
class OptionalParametersFilterTest extends TestCase
{
    /** @var OptionalParametersFilter */
    protected $filter;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->filter = new OptionalParametersFilter();
    }

    /**
     * Verifies a list of methods against expected results
     *
     * @dataProvider methodProvider
     */
    public function testMethods(string $method, bool $expectedResult): void
    {
        $this->assertSame($expectedResult, $this->filter->filter($method));
    }

    /**
     * Verifies a list of methods against expected results over subsequent calls, checking
     * that the filter behaves consistently regardless of cache optimizations
     *
     * @dataProvider methodProvider
     */
    public function testMethodsOnSubsequentCalls(string $method, bool $expectedResult): void
    {
        for ($i = 0; $i < 5; $i += 1) {
            $this->assertSame($expectedResult, $this->filter->filter($method));
        }
    }

    public function testTriggersExceptionOnUnknownMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->filter->filter(self::class . '::nonExistingMethod');
    }

    /**
     * Provides a list of methods to be checked against the filter
     *
     * @psalm-return array<array-key, array{
     *     0: string,
     *     1: bool
     * }>
     */
    public function methodProvider(): array
    {
        return [
            [self::class . '::methodWithoutParameters', true],
            [self::class . '::methodWithSingleMandatoryParameter', false],
            [self::class . '::methodWithSingleOptionalParameter', true],
            [self::class . '::methodWithMultipleMandatoryParameters', false],
            [self::class . '::methodWithMultipleOptionalParameters', true],
        ];
    }

    /**
     * Test asset method
     */
    public function methodWithoutParameters(): void
    {
    }

    /**
     * Test asset method
     */
    public function methodWithSingleMandatoryParameter(mixed $parameter): void
    {
    }

    /**
     * Test asset method
     */
    public function methodWithSingleOptionalParameter(mixed $parameter = null): void
    {
    }

    /**
     * Test asset method
     */
    public function methodWithMultipleMandatoryParameters(mixed $parameter, mixed $otherParameter): void
    {
    }

    /**
     * Test asset method
     */
    public function methodWithMultipleOptionalParameters(mixed $parameter = null, mixed $otherParameter = null): void
    {
    }
}
