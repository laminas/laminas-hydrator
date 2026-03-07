<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\ScalarTypeStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ScalarTypeStrategy::class)]
final class ScalarTypeStrategyTest extends TestCase
{
    public function testHydrate(): void
    {
        $this->assertSame(123, ScalarTypeStrategy::createToInt()->hydrate('123', null));
        $this->assertNull(ScalarTypeStrategy::createToInt()->hydrate(null, null));
        $this->assertSame(
            123.99,
            ScalarTypeStrategy::createToFloat()->hydrate('123.99', null),
        );
        $this->assertTrue(ScalarTypeStrategy::createToBoolean()->hydrate(1, null));
        $this->assertFalse(ScalarTypeStrategy::createToBoolean()->hydrate(0, null));

        $stringable = new class {
            public function __toString(): string
            {
                return 'foo';
            }
        };
        $this->assertSame('foo', ScalarTypeStrategy::createToString()->hydrate($stringable, null));
    }

    public function testExtract(): void
    {
        $this->assertSame(123, ScalarTypeStrategy::createToInt()->extract(123));
        $this->assertSame(123.99, ScalarTypeStrategy::createToFloat()->extract(123.99));
        $this->assertSame('foo', ScalarTypeStrategy::createToString()->extract('foo'));
        $this->assertTrue(ScalarTypeStrategy::createToBoolean()->extract(true));
        $this->assertFalse(ScalarTypeStrategy::createToBoolean()->extract(false));
    }
}
