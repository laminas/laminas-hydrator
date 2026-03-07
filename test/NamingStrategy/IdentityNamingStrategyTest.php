<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy;

use Laminas\Hydrator\NamingStrategy\IdentityNamingStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IdentityNamingStrategy::class)]
final class IdentityNamingStrategyTest extends TestCase
{
    #[DataProvider('getTestedNames')]
    public function testHydrate(string $name): void
    {
        $namingStrategy = new IdentityNamingStrategy();

        $this->assertSame($name, $namingStrategy->hydrate($name));
    }

    #[DataProvider('getTestedNames')]
    public function testExtract(string $name): void
    {
        $namingStrategy = new IdentityNamingStrategy();

        $this->assertSame($name, $namingStrategy->extract($name));
    }

    /**
     * Data provider
     *
     * @return string[][]
     */
    public static function getTestedNames(): array
    {
        return [
            'foo' => ['foo'],
            'bar' => ['bar'],
        ];
    }
}
