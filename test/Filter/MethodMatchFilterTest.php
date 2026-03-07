<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Filter;

use Laminas\Hydrator\Filter\MethodMatchFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(MethodMatchFilter::class)]
final class MethodMatchFilterTest extends TestCase
{
    /**
     * @return (bool|string)[][]
     * @psalm-return list<array{0: string, 1: bool}>
     */
    public static function providerFilter(): array
    {
        return [
            ['foo', true],
            ['bar', false],
            ['class::foo', true],
            ['class::bar', false],
        ];
    }

    #[DataProvider('providerFilter')]
    public function testFilter(string $methodName, bool $expected): void
    {
        $testedInstance = new MethodMatchFilter('foo', false);
        $this->assertSame($expected, $testedInstance->filter($methodName));

        $testedInstance = new MethodMatchFilter('foo', true);
        $this->assertSame(! $expected, $testedInstance->filter($methodName));
    }
}
