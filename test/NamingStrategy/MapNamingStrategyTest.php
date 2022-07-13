<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy;

use Laminas\Hydrator\Exception;
use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see MapNamingStrategy}
 *
 * @covers \Laminas\Hydrator\NamingStrategy\MapNamingStrategy
 */
class MapNamingStrategyTest extends TestCase
{
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

    /** @psalm-return iterable<array{invalidKeyArray: array<array-key, string>}> */
    public function invalidKeyArrays(): iterable
    {
        yield 'int' => [
            'invalidKeyArray' => [1 => 'foo'],
        ];
        yield 'emtpy-string' => [
            'invalidKeyArray' => ['' => 'foo'],
        ];
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
     * @dataProvider invalidKeyArrays
     */
    public function testExtractionMapConstructorRaisesExceptionWhenFlippingHydrationMapForInvalidKeys(
        array $invalidKeyArray
    ): void {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        /** @psalm-suppress MixedArgumentTypeCoercion */
        MapNamingStrategy::createFromExtractionMap($invalidKeyArray);
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

        MapNamingStrategy::createFromHydrationMap(['foo' => $invalidValue]);
    }

    /**
     * @dataProvider invalidKeyArrays
     */
    public function testHydrationMapConstructorRaisesExceptionWhenFlippingExtractionMapForInvalidKeys(
        array $invalidKeyArray
    ): void {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        /** @psalm-suppress MixedArgumentTypeCoercion */
        MapNamingStrategy::createFromHydrationMap($invalidKeyArray);
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
