<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\TypeCastStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see TypeCastStrategy}
 *
 * @covers \Laminas\Hydrator\Strategy\TypeCastStrategy
 */
final class TypeCastStrategyTest extends TestCase
{
    public function testHydrate(): void
    {
        $this->assertSame(123, TypeCastStrategy::createToInt()->hydrate('123', null));
        $this->assertNull(TypeCastStrategy::createToInt()->hydrate(null, null));
        $this->assertSame(123.99, TypeCastStrategy::createToFloat()->hydrate('123.99', null));

        $stringable = new class {
            public function __toString(): string
            {
                return 'foo';
            }
        };
        $this->assertSame('foo', TypeCastStrategy::createToString()->hydrate($stringable, null));
    }

    public function testExtract(): void
    {
        $this->assertSame(123, TypeCastStrategy::createToInt()->extract(123));
        $this->assertSame(123.99, TypeCastStrategy::createToFloat()->extract(123.99));
        $this->assertSame('foo', TypeCastStrategy::createToString()->extract('foo'));
    }
}
