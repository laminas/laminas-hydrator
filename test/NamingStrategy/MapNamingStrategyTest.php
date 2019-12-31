<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy;

use Error;
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
    public function invalidMapValues() : iterable
    {
        yield 'null'       => [null];
        yield 'true'       => [true];
        yield 'false'      => [false];
        yield 'zero-float' => [0.0];
        yield 'float'      => [1.1];
        yield 'array'      => [['foo']];
        yield 'object'     => [(object) ['foo' => 'bar']];
    }

    public function invalidKeyValues() : iterable
    {
        yield 'null'       => [null];
        yield 'true'       => [true];
        yield 'false'      => [false];
        yield 'zero-float' => [0.0];
        yield 'float'      => [1.1];
    }

    /**
     * @dataProvider invalidMapValues
     * @param mixed $invalidValue
     */
    public function testExtractionMapConstructorRaisesExceptionWhenFlippingHydrationMapForInvalidValues($invalidValue)
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        MapNamingStrategy::createFromExtractionMap(['foo' => $invalidValue]);
    }

    /**
     * @dataProvider invalidKeyValues
     * @param mixed $invalidKey
     */
    public function testExtractionMapConstructorRaisesExceptionWhenFlippingHydrationMapForInvalidKeys($invalidKey)
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        MapNamingStrategy::createFromExtractionMap([$invalidKey => 'foo']);
    }

    /**
     * @dataProvider invalidMapValues
     * @param mixed $invalidValue
     */
    public function testHydrationMapConstructorRaisesExceptionWhenFlippingExtractionMapForInvalidValues($invalidValue)
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        MapNamingStrategy::createFromHydrationMap(['foo' => $invalidValue]);
    }

    /**
     * @dataProvider invalidKeyValues
     * @param mixed $invalidKey
     */
    public function testHydrationMapConstructorRaisesExceptionWhenFlippingExtractionMapForInvalidKeys($invalidKey)
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be flipped');

        MapNamingStrategy::createFromHydrationMap([$invalidKey => 'foo']);
    }

    public function testExtractReturnsVerbatimWhenEmptyExtractionMapProvided()
    {
        $strategy = MapNamingStrategy::createFromExtractionMap([]);
        $this->assertEquals('some_stuff', $strategy->extract('some_stuff'));
    }

    public function testHydrateReturnsVerbatimWhenEmptyHydrationMapProvided()
    {
        $strategy = MapNamingStrategy::createFromHydrationMap([]);
        $this->assertEquals('some_stuff', $strategy->hydrate('some_stuff'));
    }

    public function testExtractUsesProvidedExtractionMap()
    {
        $strategy = MapNamingStrategy::createFromExtractionMap(['stuff3' => 'stuff4']);
        $this->assertEquals('stuff4', $strategy->extract('stuff3'));
    }

    public function testExtractUsesFlippedHydrationMapWhenOnlyHydrationMapProvided()
    {
        $strategy = MapNamingStrategy::createFromHydrationMap(['stuff3' => 'stuff4']);
        $this->assertEquals('stuff3', $strategy->extract('stuff4'));
    }

    public function testHydrateUsesProvidedHydrationMap()
    {
        $strategy = MapNamingStrategy::createFromHydrationMap(['stuff3' => 'stuff4']);
        $this->assertEquals('stuff4', $strategy->hydrate('stuff3'));
    }

    public function testHydrateUsesFlippedExtractionMapOnlyExtractionMapProvided()
    {
        $strategy = MapNamingStrategy::createFromExtractionMap(['foo' => 'bar']);
        $this->assertEquals('foo', $strategy->hydrate('bar'));
    }

    public function testHydrateAndExtractUseAsymmetricMapProvided()
    {
        $strategy = MapNamingStrategy::createFromAsymmetricMap(['foo' => 'bar'], ['bat' => 'baz']);
        $this->assertEquals('bar', $strategy->extract('foo'));
        $this->assertEquals('baz', $strategy->hydrate('bat'));
    }
}
