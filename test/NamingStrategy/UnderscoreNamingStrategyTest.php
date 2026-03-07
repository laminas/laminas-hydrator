<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy;

use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnderscoreNamingStrategy::class)]
final class UnderscoreNamingStrategyTest extends TestCase
{
    public function testNameHydratesToCamelCase(): void
    {
        $strategy = new UnderscoreNamingStrategy();
        $this->assertSame('fooBarBaz', $strategy->hydrate('foo_bar_baz'));
    }

    public function testNameExtractsToUnderscore(): void
    {
        $strategy = new UnderscoreNamingStrategy();
        $this->assertSame('foo_bar_baz', $strategy->extract('fooBarBaz'));
    }

    #[Group('6422')]
    #[Group('6420')]
    public function testNameHydratesToStudlyCaps(): void
    {
        $strategy = new UnderscoreNamingStrategy();

        $this->assertSame('fooBarBaz', $strategy->hydrate('Foo_Bar_Baz'));
    }
}
