<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Strategy\SerializableStrategy;
use Laminas\Serializer\Adapter\PhpSerialize;
use Laminas\Serializer\Serializer;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * @covers Laminas\Hydrator\Strategy\SerializableStrategy<extended>
 */
class SerializableStrategyTest extends TestCase
{
    public function testCannotUseBadArgumentSerializer()
    {
        $this->expectException(InvalidArgumentException::class);
        $serializerStrategy = new SerializableStrategy(false);
    }

    public function testUseBadSerializerObject()
    {
        $serializer = Serializer::factory('phpserialize');
        $serializerStrategy = new SerializableStrategy($serializer);
        $this->assertEquals($serializer, $serializerStrategy->getSerializer());
    }

    public function testUseBadSerializerString()
    {
        $serializerStrategy = new SerializableStrategy('phpserialize');
        $this->assertEquals(PhpSerialize::class, get_class($serializerStrategy->getSerializer()));
    }

    public function testCanSerialize()
    {
        $serializer = Serializer::factory('phpserialize');
        $serializerStrategy = new SerializableStrategy($serializer);
        $serialized = $serializerStrategy->extract('foo');
        $this->assertEquals($serialized, 's:3:"foo";');
    }

    public function testCanUnserialize()
    {
        $serializer = Serializer::factory('phpserialize');
        $serializerStrategy = new SerializableStrategy($serializer);
        $serialized = $serializerStrategy->hydrate('s:3:"foo";');
        $this->assertEquals($serialized, 'foo');
    }
}
