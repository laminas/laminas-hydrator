<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\NamingStrategy;

use InvalidArgumentException;
use Laminas\Hydrator\NamingStrategy\MapNamingStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @covers Laminas\Hydrator\NamingStrategy\MapNamingStrategy<extended>
 */
class MapNamingStrategyTest extends TestCase
{
    public function testHydrateMap()
    {
        $namingStrategy = new MapNamingStrategy(['foo' => 'bar']);

        $this->assertEquals('bar', $namingStrategy->hydrate('foo'));
        $this->assertEquals('foo', $namingStrategy->extract('bar'));
    }

    public function testHydrateAndExtractMaps()
    {
        $namingStrategy = new MapNamingStrategy(
            ['foo' => 'foo-hydrated'],
            ['bar' => 'bar-extracted']
        );

        $this->assertEquals('foo-hydrated', $namingStrategy->hydrate('foo'));
        $this->assertEquals('bar-extracted', $namingStrategy->extract('bar'));
    }

    public function testSingleMapInvalidValue()
    {
        $this->expectException(InvalidArgumentException::class);
        new MapNamingStrategy(['foo' => 3.1415]);
    }

    public function testReturnSpecifiedValue()
    {
        $namingStrategy = new MapNamingStrategy(
            [ 'foo' => 'foo-hydrated'],
            [ 'bar' => 'bar-extracted']
        );

        $name = 'foobar';

        $this->assertEquals($name, $namingStrategy->extract($name));
        $this->assertEquals($name, $namingStrategy->hydrate($name));
    }
}
