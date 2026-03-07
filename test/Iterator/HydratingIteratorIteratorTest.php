<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Iterator;

use ArrayIterator;
use ArrayObject;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Iterator\HydratingIteratorIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HydratingIteratorIterator::class)]
final class HydratingIteratorIteratorTest extends TestCase
{
    public function testHydratesObjectAndClonesOnCurrent(): void
    {
        $data = [
            ['foo' => 'bar'],
            ['baz' => 'bat'],
        ];

        $iterator = new ArrayIterator($data);
        $object   = new ArrayObject();

        $hydratingIterator = new HydratingIteratorIterator(new ArraySerializableHydrator(), $iterator, $object);

        $hydratingIterator->rewind();
        $this->assertEquals(new ArrayObject($data[0]), $hydratingIterator->current());
        $this->assertNotSame(
            $object,
            $hydratingIterator->current(),
            'Hydrating Iterator did not clone the object'
        );

        $hydratingIterator->next();
        $this->assertEquals(new ArrayObject($data[1]), $hydratingIterator->current());
    }

    public function testEmptyIterator(): void
    {
        $data      = [];
        $iterator  = new ArrayIterator($data);
        $prototype = new ArrayObject();

        $hydratingIterator = new HydratingIteratorIterator(
            new ArraySerializableHydrator(),
            $iterator,
            $prototype
        );

        $hydratingIterator->rewind();

        $this->assertNotInstanceOf(ArrayObject::class, $hydratingIterator->current());
    }

    public function testUsingStringForObjectName(): void
    {
        $data = [
            ['foo' => 'bar'],
        ];

        $iterator = new ArrayIterator($data);

        $hydratingIterator = new HydratingIteratorIterator(
            new ArraySerializableHydrator(),
            $iterator,
            ArrayObject::class
        );

        $hydratingIterator->rewind();
        $this->assertEquals(new ArrayObject($data[0]), $hydratingIterator->current());
    }

    public function testThrowingInvalidArgumentExceptionWhenSettingPrototypeToInvalidClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new HydratingIteratorIterator(
            new ArraySerializableHydrator(),
            new ArrayIterator(),
            'not a real class'
        );
    }
}
