<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\Iterator;

use ArrayObject;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Iterator\HydratingArrayIterator;
use PHPUnit\Framework\TestCase;

/**
 * @covers Laminas\Hydrator\Iterator\HydratingArrayIterator
 */
class HydratingArrayIteratorTest extends TestCase
{
    public function testHydratesObjectAndClonesOnCurrent(): void
    {
        $data = [
            ['foo' => 'bar'],
            ['baz' => 'bat'],
        ];

        $object   = new ArrayObject();

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

        $hydratingIterator = new HydratingArrayIterator(new ArraySerializableHydrator(), $data, '\ArrayObject');

        $hydratingIterator->rewind();
        $this->assertEquals(new ArrayObject($data[0]), $hydratingIterator->current());
    }

    public function testThrowingInvalidArgumentExceptionWhenSettingPrototypeToInvalidClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $hydratingIterator = new HydratingArrayIterator(new ArraySerializableHydrator(), [], 'not a real class');
    }
}
