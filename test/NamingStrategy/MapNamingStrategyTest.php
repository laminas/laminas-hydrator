<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy;

use Generator;
use Laminas\Hydrator\Exception;
use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(MapNamingStrategy::class)]
class MapNamingStrategyTest extends TestCase
{
    /** @return Generator<string, list<mixed>> */
    public static function invalidMapValues(): Generator
    {
        yield 'null'       => [null];
        yield 'true'       => [true];
        yield 'false'      => [false];
        yield 'zero-float' => [0.0];
        yield 'float'      => [1.1];
        yield 'array'      => [['foo']];
        yield 'object'     => [(object) ['foo' => 'bar']];
    }

    /** @psalm-return Generator<string, array{invalidKeyArray: array<array-key, string>}> */
    public static function invalidKeyArrays(): Generator
    {
        yield 'int' => [
            'invalidKeyArray' => [1 => 'foo'],
        ];
        yield 'emtpy-string' => [
            'invalidKeyArray' => ['' => 'foo'],
        ];
    }

    #[DataProvider('invalidMapValues')]
    public function testExtractionMapConstructorRaisesExceptionWhenFlippingHydrationMapForInvalidValues(
        mixed $invalidValue
    ): void {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        /** @psalm-suppress MixedArgumentTypeCoercion */
        MapNamingStrategy::createFromExtractionMap(['foo' => $invalidValue]);
    }

    #[DataProvider('invalidKeyArrays')]
    public function testExtractionMapConstructorRaisesExceptionWhenFlippingHydrationMapForInvalidKeys(
        array $invalidKeyArray
    ): void {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        /** @psalm-suppress MixedArgumentTypeCoercion */
        MapNamingStrategy::createFromExtractionMap($invalidKeyArray);
    }

    #[DataProvider('invalidMapValues')]
    public function testHydrationMapConstructorRaisesExceptionWhenFlippingExtractionMapForInvalidValues(
        mixed $invalidValue
    ): void {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        /** @psalm-suppress MixedArgumentTypeCoercion */
        MapNamingStrategy::createFromHydrationMap(['foo' => $invalidValue]);
    }

    #[DataProvider('invalidKeyArrays')]
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
        self::assertEquals('some_stuff', $strategy->extract('some_stuff'));
    }

    public function testHydrateReturnsVerbatimWhenEmptyHydrationMapProvided(): void
    {
        $strategy = MapNamingStrategy::createFromHydrationMap([]);
        self::assertEquals('some_stuff', $strategy->hydrate('some_stuff'));
    }

    public function testExtractUsesProvidedExtractionMap(): void
    {
        $strategy = MapNamingStrategy::createFromExtractionMap(['stuff3' => 'stuff4']);
        self::assertEquals('stuff4', $strategy->extract('stuff3'));
    }

    public function testExtractUsesFlippedHydrationMapWhenOnlyHydrationMapProvided(): void
    {
        $strategy = MapNamingStrategy::createFromHydrationMap(['stuff3' => 'stuff4']);
        self::assertEquals('stuff3', $strategy->extract('stuff4'));
    }

    public function testHydrateUsesProvidedHydrationMap(): void
    {
        $strategy = MapNamingStrategy::createFromHydrationMap(['stuff3' => 'stuff4']);
        self::assertEquals('stuff4', $strategy->hydrate('stuff3'));
    }

    public function testHydrateUsesFlippedExtractionMapOnlyExtractionMapProvided(): void
    {
        $strategy = MapNamingStrategy::createFromExtractionMap(['foo' => 'bar']);
        self::assertEquals('foo', $strategy->hydrate('bar'));
    }

    public function testHydrateAndExtractUseAsymmetricMapProvided(): void
    {
        $strategy = MapNamingStrategy::createFromAsymmetricMap(['foo' => 'bar'], ['bat' => 'baz']);
        self::assertEquals('bar', $strategy->extract('foo'));
        self::assertEquals('baz', $strategy->hydrate('bat'));
    }
}
