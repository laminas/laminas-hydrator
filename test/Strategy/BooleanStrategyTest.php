<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Strategy\BooleanStrategy;
use PHPUnit\Framework\TestCase;
use stdClass;

class BooleanStrategyTest extends TestCase
{
    public function testConstructorWithValidInteger(): void
    {
        $this->assertInstanceOf(BooleanStrategy::class, new BooleanStrategy(1, 0));
    }

    public function testConstructorWithValidString(): void
    {
        $this->assertInstanceOf(BooleanStrategy::class, new BooleanStrategy('true', 'false'));
    }

    public function testExceptionOnWrongTrueValueInConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected int or string as $trueValue.');

        new BooleanStrategy(true, 0);
    }

    public function testExceptionOnWrongFalseValueInConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected int or string as $falseValue.');

        new BooleanStrategy(1, false);
    }

    public function testExtractString(): void
    {
        $hydrator = new BooleanStrategy('true', 'false');
        $this->assertEquals('true', $hydrator->extract(true));
        $this->assertEquals('false', $hydrator->extract(false));
    }

    public function testExtractInteger(): void
    {
        $hydrator = new BooleanStrategy(1, 0);

        $this->assertEquals(1, $hydrator->extract(true));
        $this->assertEquals(0, $hydrator->extract(false));
    }

    public function testExtractThrowsExceptionOnUnknownValue(): void
    {
        $hydrator = new BooleanStrategy(1, 0);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to extract');

        $hydrator->extract(5);
    }

    public function testHydrateString(): void
    {
        $hydrator = new BooleanStrategy('true', 'false');
        $this->assertEquals(true, $hydrator->hydrate('true'));
        $this->assertEquals(false, $hydrator->hydrate('false'));
    }

    public function testHydrateInteger(): void
    {
        $hydrator = new BooleanStrategy(1, 0);
        $this->assertEquals(true, $hydrator->hydrate(1));
        $this->assertEquals(false, $hydrator->hydrate(0));
    }

    public function testHydrateBool(): void
    {
        $hydrator = new BooleanStrategy(1, 0);
        $this->assertEquals(true, $hydrator->hydrate(true));
        $this->assertEquals(false, $hydrator->hydrate(false));
    }

    public function testHydrateUnexpectedValueThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected value');
        $hydrator = new BooleanStrategy(1, 0);
        $hydrator->hydrate(2);
    }

    public function testHydrateInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to hydrate');
        $hydrator = new BooleanStrategy(1, 0);
        /** @psalm-suppress InvalidArgument */
        $hydrator->hydrate(new stdClass());
    }
}
