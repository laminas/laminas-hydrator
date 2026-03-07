<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\NullableStrategy;
use Laminas\Hydrator\Strategy\StrategyInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NullableStrategy::class)]
final class NullableStrategyTest extends TestCase
{
    public function testExtractNonNullAndNonEmptyValue(): void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->once())
            ->method('extract')
            ->with('original value')
            ->willReturn('extracted value');
        $nullableStrategy = new NullableStrategy($strategy, false);

        $this->assertEquals('extracted value', $nullableStrategy->extract('original value'));
    }

    public function testExtractNullValue(): void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->never())
            ->method('extract');
        $nullableStrategy = new NullableStrategy($strategy, false);

        $this->assertNull($nullableStrategy->extract(null));
    }

    public function testExtractEmptyValueAsNull(): void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->never())
            ->method('extract');
        $nullableStrategy = new NullableStrategy($strategy, true);

        $this->assertNull($nullableStrategy->extract(''));
    }

    public function testExtractEmptyValueByHydrator(): void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->once())
            ->method('extract')
            ->with('')
            ->willReturn('extracted empty value');

        $nullableStrategy = new NullableStrategy($strategy, false);

        $this->assertEquals('extracted empty value', $nullableStrategy->extract(''));
    }

    public function testHydrateNonNullValue(): void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->once())
            ->method('hydrate')
            ->with('original value')
            ->willReturn('hydrated value');
        $nullableStrategy = new NullableStrategy($strategy, false);

        $this->assertEquals('hydrated value', $nullableStrategy->hydrate('original value'));
    }

    public function testHydrateNullValue(): void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->never())
            ->method('hydrate');
        $nullableStrategy = new NullableStrategy($strategy, false);

        $this->assertNull($nullableStrategy->hydrate(null));
    }

    public function testHydrateEmptyValueAsNull(): void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->never())
            ->method('hydrate');
        $nullableStrategy = new NullableStrategy($strategy, true);

        $this->assertNull($nullableStrategy->hydrate(''));
    }

    public function testHydrateEmptyValueByHydrator(): void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects($this->once())
            ->method('hydrate')
            ->with('')
            ->willReturn('hydrated empty value');
        $nullableStrategy = new NullableStrategy($strategy, false);

        $this->assertEquals('hydrated empty value', $nullableStrategy->hydrate(''));
    }
}
