<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\NullableStrategy;
use Laminas\Hydrator\Strategy\StrategyInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\Hydrator\Strategy\NullableStrategy
 */
class NullableStrategyTest extends TestCase
{
    /**
     * @var bool
     */
    protected $backupStaticAttributes = false;

    /**
     * @var bool
     */
    protected $runTestInSeparateProcess = false;

    public function testExtractNonNullValue() : void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects(self::once())
            ->method('extract')
            ->with('original value')
            ->willReturn('extracted value');
        $nullableStrategy = new NullableStrategy($strategy);

        self::assertEquals('extracted value', $nullableStrategy->extract('original value'));
    }

    public function testExtractNullValue() : void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects(self::never())
            ->method('extract');
        $nullableStrategy = new NullableStrategy($strategy);

        self::assertNull($nullableStrategy->extract(null));
    }

    public function testHydrateNonNullValue() : void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects(self::once())
            ->method('hydrate')
            ->with('original value')
            ->willReturn('hydrated value');
        $nullableStrategy = new NullableStrategy($strategy);

        self::assertEquals('hydrated value', $nullableStrategy->hydrate('original value'));
    }

    public function testHydrateNullValue() : void
    {
        $strategy = $this->createMock(StrategyInterface::class);
        $strategy->expects(self::never())
            ->method('hydrate');
        $nullableStrategy = new NullableStrategy($strategy);

        self::assertNull($nullableStrategy->hydrate(null));
    }
}
