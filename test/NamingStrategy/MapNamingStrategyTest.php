<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy;

use Laminas\Hydrator\Exception;
use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;
use PHPUnit\Framework\TestCase;

use function error_reporting;

use const E_ALL;
use const E_DEPRECATED;
use const PHP_VERSION_ID;

/**
 * Tests for {@see MapNamingStrategy}
 *
 * @covers \Laminas\Hydrator\NamingStrategy\MapNamingStrategy
 */
class MapNamingStrategyTest extends TestCase
{
    /** @var int */
    private $errorLevel;

    public function setUp(): void
    {
        $this->errorLevel = error_reporting();
    }

    public function tearDown(): void
    {
        error_reporting($this->errorLevel);
    }

    public function invalidMapValues(): iterable
    {
        yield 'null'       => [null];
        yield 'true'       => [true];
        yield 'false'      => [false];
        yield 'zero-float' => [0.0];
        yield 'float'      => [1.1];
        yield 'array'      => [['foo']];
        yield 'object'     => [(object) ['foo' => 'bar']];
    }

    /** @psalm-return iterable<string, array{0: mixed, 1: null|string}> */
    public function invalidKeyValues(): iterable
    {
        yield 'null'       => [null, null];
        yield 'true'       => [true, null];
        yield 'false'      => [false, null];
        yield 'zero-float' => [0.0, null];
        if (PHP_VERSION_ID >= 80100) {
            error_reporting(E_ALL & ~E_DEPRECATED);
        }
        yield 'float'      => [1.1, null];
    }

    /**
     * @dataProvider invalidMapValues
     * @param mixed $invalidValue
     */
    public function testExtractionMapConstructorRaisesExceptionWhenFlippingHydrationMapForInvalidValues(
        $invalidValue
    ): void {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        MapNamingStrategy::createFromExtractionMap(['foo' => $invalidValue]);
    }

    /**
     * @dataProvider invalidKeyValues
     * @param mixed $invalidKey
     */
    public function testExtractionMapConstructorRaisesExceptionWhenFlippingHydrationMapForInvalidKeys(
        $invalidKey,
    ): void {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        /** @psalm-suppress MixedArrayOffset */
        MapNamingStrategy::createFromExtractionMap([$invalidKey => 'foo']);
    }

    /**
     * @dataProvider invalidMapValues
     * @param mixed $invalidValue
     */
    public function testHydrationMapConstructorRaisesExceptionWhenFlippingExtractionMapForInvalidValues(
        $invalidValue
    ): void {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        /** @psalm-suppress MixedArrayOffset */
        MapNamingStrategy::createFromHydrationMap(['foo' => $invalidValue]);
    }

    /**
     * @dataProvider invalidKeyValues
     * @param mixed $invalidKey
     */
    public function testHydrationMapConstructorRaisesExceptionWhenFlippingExtractionMapForInvalidKeys(
        $invalidKey,
        ?string $errorMessage
    ): void {
        if (null === $errorMessage) {
            // PHP < 8.1, or PHP >= 8.1 AND non-float value
            $this->expectException(Exception\InvalidArgumentException::class);
            $this->expectExceptionMessage('can not be flipped');
        } else {
            $this->expectError();
            $this->expectErrorMessage($errorMessage);
        }

        /** @psalm-suppress MixedArrayOffset */
        MapNamingStrategy::createFromHydrationMap([$invalidKey => 'foo']);
    }

    public function testExtractReturnsVerbatimWhenEmptyExtractionMapProvided(): void
    {
        $strategy = MapNamingStrategy::createFromExtractionMap([]);
        $this->assertEquals('some_stuff', $strategy->extract('some_stuff'));
    }

    public function testHydrateReturnsVerbatimWhenEmptyHydrationMapProvided(): void
    {
        $strategy = MapNamingStrategy::createFromHydrationMap([]);
        $this->assertEquals('some_stuff', $strategy->hydrate('some_stuff'));
    }

    public function testExtractUsesProvidedExtractionMap(): void
    {
        $strategy = MapNamingStrategy::createFromExtractionMap(['stuff3' => 'stuff4']);
        $this->assertEquals('stuff4', $strategy->extract('stuff3'));
    }

    public function testExtractUsesFlippedHydrationMapWhenOnlyHydrationMapProvided(): void
    {
        $strategy = MapNamingStrategy::createFromHydrationMap(['stuff3' => 'stuff4']);
        $this->assertEquals('stuff3', $strategy->extract('stuff4'));
    }

    public function testHydrateUsesProvidedHydrationMap(): void
    {
        $strategy = MapNamingStrategy::createFromHydrationMap(['stuff3' => 'stuff4']);
        $this->assertEquals('stuff4', $strategy->hydrate('stuff3'));
    }

    public function testHydrateUsesFlippedExtractionMapOnlyExtractionMapProvided(): void
    {
        $strategy = MapNamingStrategy::createFromExtractionMap(['foo' => 'bar']);
        $this->assertEquals('foo', $strategy->hydrate('bar'));
    }

    public function testHydrateAndExtractUseAsymmetricMapProvided(): void
    {
        $strategy = MapNamingStrategy::createFromAsymmetricMap(['foo' => 'bar'], ['bat' => 'baz']);
        $this->assertEquals('bar', $strategy->extract('foo'));
        $this->assertEquals('baz', $strategy->hydrate('bat'));
    }
}
