<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\ClosureStrategy;
use Laminas\Hydrator\Strategy\StrategyChain;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\Hydrator\Strategy\StrategyChain
 */
class StrategyChainTest extends TestCase
{
    public function testEmptyStrategyChainReturnsOriginalValue(): void
    {
        $chain = new StrategyChain([]);
        $this->assertEquals('something', $chain->hydrate('something'));
        $this->assertEquals('something', $chain->extract('something'));
    }

    public function testExtract(): void
    {
        $chain = new StrategyChain([
            new ClosureStrategy(
                static fn(int $value): int => $value % 12
            ),
            new ClosureStrategy(
                static fn(int $value): int => $value % 9
            ),
        ]);
        $this->assertEquals(3, $chain->extract(87));

        $chain = new StrategyChain([
            new ClosureStrategy(
                static fn(int $value): int => $value % 8
            ),
            new ClosureStrategy(
                static fn(int $value): int => $value % 3
            ),
        ]);
        $this->assertEquals(1, $chain->extract(20));

        $chain = new StrategyChain([
            new ClosureStrategy(
                static fn(int $value): int => $value % 7
            ),
            new ClosureStrategy(
                static fn(int $value): int => $value % 6
            ),
        ]);
        $this->assertEquals(2, $chain->extract(30));
    }

    public function testHydrate(): void
    {
        $chain = new StrategyChain([
            new ClosureStrategy(
                null,
                static fn(int $value): int => $value % 3
            ),
            new ClosureStrategy(
                null,
                static fn(int $value): int => $value % 7
            ),
        ]);
        $this->assertEquals(0, $chain->hydrate(87));

        $chain = new StrategyChain([
            new ClosureStrategy(
                null,
                static fn(int $value): int => $value % 8
            ),
            new ClosureStrategy(
                null,
                static fn(int $value): int => $value % 3
            ),
        ]);
        $this->assertEquals(2, $chain->hydrate(20));

        $chain = new StrategyChain([
            new ClosureStrategy(
                null,
                static fn(int $value): int => $value % 4
            ),
            new ClosureStrategy(
                null,
                static fn(int $value): int => $value % 9
            ),
        ]);
        $this->assertEquals(3, $chain->hydrate(30));
    }
}
