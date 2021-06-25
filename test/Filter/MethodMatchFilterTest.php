<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Filter;

use Laminas\Hydrator\Filter\MethodMatchFilter;
use PHPUnit\Framework\TestCase;

class MethodMatchFilterTest extends TestCase
{
    /**
     * @return (bool|string)[][]
     * @psalm-return list<array{0: string, 1: bool}>
     */
    public function providerFilter(): array
    {
        return [
            ['foo', true],
            ['bar', false],
            ['class::foo', true],
            ['class::bar', false],
        ];
    }

    /**
     * @dataProvider providerFilter
     */
    public function testFilter(string $methodName, bool $expected): void
    {
        $testedInstance = new MethodMatchFilter('foo', false);
        self::assertEquals($expected, $testedInstance->filter($methodName));

        $testedInstance = new MethodMatchFilter('foo', true);
        self::assertEquals(! $expected, $testedInstance->filter($methodName));
    }
}
