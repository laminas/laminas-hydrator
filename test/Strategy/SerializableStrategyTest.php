<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\SerializableStrategy;
use Laminas\Serializer\Adapter\PhpSerialize;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SerializableStrategy::class)]
class SerializableStrategyTest extends TestCase
{
    public function testUseBadSerializerObject(): void
    {
        $serializer         = new PhpSerialize();
        $serializerStrategy = new SerializableStrategy($serializer);
        $this->assertEquals($serializer, $serializerStrategy->getSerializer());
    }

    public function testUseBadSerializerString(): void
    {
        $serializerStrategy = new SerializableStrategy(new PhpSerialize());
        $this->assertEquals(PhpSerialize::class, $serializerStrategy->getSerializer()::class);
    }

    public function testCanSerialize(): void
    {
        $serializer         = new PhpSerialize();
        $serializerStrategy = new SerializableStrategy($serializer);
        $serialized         = $serializerStrategy->extract('foo');
        $this->assertEquals($serialized, 's:3:"foo";');
    }

    public function testCanUnserialize(): void
    {
        $serializer         = new PhpSerialize();
        $serializerStrategy = new SerializableStrategy($serializer);
        $serialized         = $serializerStrategy->hydrate('s:3:"foo";');
        $this->assertEquals($serialized, 'foo');
    }
}
