<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Exception\DomainException;
use Laminas\Hydrator\Strategy\BackedEnumStrategy;
use Laminas\Hydrator\Strategy\Exception\InvalidArgumentException;
use LaminasTest\Hydrator\Strategy\TestAsset\TestBackedEnum;
use LaminasTest\Hydrator\Strategy\TestAsset\TestUnitEnum;
use PHPUnit\Framework\TestCase;

use const PHP_VERSION_ID;

/**
 * @uses \Laminas\Hydrator\Exception\DomainException
 * @uses \Laminas\Hydrator\Strategy\Exception\InvalidArgumentException
 *
 * @covers \Laminas\Hydrator\Strategy\BackedEnumStrategy
 */
final class BackedEnumStrategyTest extends TestCase
{
    public function testConstructInvalidPhpVersionThrowsException(): void
    {
        if (PHP_VERSION_ID >= 80100) {
            self::markTestSkipped("PHP >=8.1 detected");
        }

        self::expectException(DomainException::class);
        new BackedEnumStrategy(TestBackedEnum::class);
    }

    public function testConstructUnitEnumThrowsException(): void
    {
        $this->checkVersion();

        self::expectException(InvalidArgumentException::class);
        new BackedEnumStrategy(TestUnitEnum::class);
    }

    public function testExtractInvalidValueThrowsException(): void
    {
        $this->checkVersion();

        $strategy = new BackedEnumStrategy(TestBackedEnum::class);
        self::expectException(InvalidArgumentException::class);
        $strategy->extract(TestUnitEnum::One);
    }

    public function testExtractExtractsValue(): void
    {
        $this->checkVersion();

        $strategy = new BackedEnumStrategy(TestBackedEnum::class);
        $actual   = $strategy->extract(TestBackedEnum::One);
        self::assertSame('one', $actual);
    }

    public function testHydrateEnumReturnsEnum(): void
    {
        $this->checkVersion();

        $expected = TestBackedEnum::Two;
        $strategy = new BackedEnumStrategy(TestBackedEnum::class);
        $actual   = $strategy->hydrate($expected, null);
        self::assertSame(TestBackedEnum::Two, $actual);
    }

    public function testHydrateNonScalarThrowsException(): void
    {
        $this->checkVersion();

        $strategy = new BackedEnumStrategy(TestBackedEnum::class);
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage("Value must be scalar; array provided");
        $strategy->hydrate([], null);
    }

    public function testHydrateNonCaseThrowsException(): void
    {
        $this->checkVersion();

        $strategy = new BackedEnumStrategy(TestBackedEnum::class);
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage("Value 'three' is not a valid scalar value for " . TestBackedEnum::class);
        $strategy->hydrate('three', null);
    }

    public function testHydrateValueReturnsEnum(): void
    {
        $this->checkVersion();

        $strategy = new BackedEnumStrategy(TestBackedEnum::class);
        $actual   = $strategy->hydrate('two', null);
        self::assertSame(TestBackedEnum::Two, $actual);
    }

    private function checkVersion(): void
    {
        if (PHP_VERSION_ID < 80100) {
            self::markTestSkipped("PHP 8.1+ required");
        }
    }
}
