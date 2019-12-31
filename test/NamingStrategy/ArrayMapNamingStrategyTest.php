<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\NamingStrategy;

use Laminas\Hydrator\NamingStrategy\ArrayMapNamingStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see ArrayMapNamingStrategy}
 *
 * @covers \Laminas\Hydrator\NamingStrategy\ArrayMapNamingStrategy
 */
class ArrayMapNamingStrategyTest extends TestCase
{
    public function testGetSameNameWithEmptyMap()
    {
        $strategy = new ArrayMapNamingStrategy([]);
        $this->assertEquals('some_stuff', $strategy->hydrate('some_stuff'));
        $this->assertEquals('some_stuff', $strategy->extract('some_stuff'));
    }

    public function testExtract()
    {
        $strategy = new ArrayMapNamingStrategy(['stuff3' => 'stuff4']);
        $this->assertEquals('stuff4', $strategy->extract('stuff3'));
    }

    public function testHydrate()
    {
        $strategy = new ArrayMapNamingStrategy(['foo' => 'bar']);
        $this->assertEquals('foo', $strategy->hydrate('bar'));
    }
}
