<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\SerializableStrategy;
use Laminas\Serializer\Adapter\PhpSerialize;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SerializableStrategy::class)]
final class SerializableStrategyTest extends TestCase
{
    public function testCanSerialize(): void
    {
        $serializer         = new PhpSerialize();
        $serializerStrategy = new SerializableStrategy($serializer);
        $serialized         = $serializerStrategy->extract('foo');
        $this->assertSame('s:3:"foo";', $serialized);
    }

    public function testCanUnserialize(): void
    {
        $serializer         = new PhpSerialize();
        $serializerStrategy = new SerializableStrategy($serializer);
        $serialized         = $serializerStrategy->hydrate('s:3:"foo";');
        $this->assertEquals('foo', $serialized);
    }
}
