<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Iterator;

use ArrayObject;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Iterator\HydratingArrayIterator;
use PHPUnit\Framework\TestCase;

class HydratingArrayIteratorTest extends TestCase
{
    public function testHydratesObjectAndClonesOnCurrent(): void
    {
        $data = [
            ['foo' => 'bar'],
            ['baz' => 'bat'],
        ];

        $object = new ArrayObject();

        $hydratingIterator = new HydratingArrayIterator(new ArraySerializableHydrator(), $data, $object);

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

    public function testUsingStringForObjectName(): void
    {
        $data = [
            ['foo' => 'bar'],
        ];

        $hydratingIterator = new HydratingArrayIterator(new ArraySerializableHydrator(), $data, ArrayObject::class);

        $hydratingIterator->rewind();
        $this->assertEquals(new ArrayObject($data[0]), $hydratingIterator->current());
    }

    public function testThrowingInvalidArgumentExceptionWhenSettingPrototypeToInvalidClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new HydratingArrayIterator(new ArraySerializableHydrator(), [], 'not a real class');
    }
}
