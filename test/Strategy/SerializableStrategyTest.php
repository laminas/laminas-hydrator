<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Strategy\SerializableStrategy;
use Laminas\Serializer\Adapter\PhpSerialize;
use Laminas\Serializer\Serializer;
use PHPUnit\Framework\TestCase;

use function get_class;

/**
 * @covers Laminas\Hydrator\Strategy\SerializableStrategy
 */
class SerializableStrategyTest extends TestCase
{
    public function testCannotUseBadArgumentSerializer(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @psalm-suppress InvalidArgument */
        new SerializableStrategy(false);
    }

    public function testUseBadSerializerObject(): void
    {
        $serializer         = Serializer::factory('phpserialize');
        $serializerStrategy = new SerializableStrategy($serializer);
        $this->assertEquals($serializer, $serializerStrategy->getSerializer());
    }

    public function testUseBadSerializerString(): void
    {
        $serializerStrategy = new SerializableStrategy('phpserialize');
        $this->assertEquals(PhpSerialize::class, get_class($serializerStrategy->getSerializer()));
    }

    public function testCanSerialize(): void
    {
        $serializer         = Serializer::factory('phpserialize');
        $serializerStrategy = new SerializableStrategy($serializer);
        $serialized         = $serializerStrategy->extract('foo');
        $this->assertEquals($serialized, 's:3:"foo";');
    }

    public function testCanUnserialize(): void
    {
        $serializer         = Serializer::factory('phpserialize');
        $serializerStrategy = new SerializableStrategy($serializer);
        $serialized         = $serializerStrategy->hydrate('s:3:"foo";');
        $this->assertEquals($serialized, 'foo');
    }
}
