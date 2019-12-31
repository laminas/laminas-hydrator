<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\Iterator;

use ArrayObject;
use Laminas\Hydrator\ArraySerializable;
use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\Iterator\HydratingArrayIterator;
use PHPUnit\Framework\TestCase;

/**
 * @covers Laminas\Hydrator\Iterator\HydratingArrayIterator<extended>
 */
class HydratingArrayIteratorTest extends TestCase
{
    public function testHydratesObjectAndClonesOnCurrent()
    {
        $data = [
            ['foo' => 'bar'],
            ['baz' => 'bat'],
        ];

        $object   = new ArrayObject();

        $hydratingIterator = new HydratingArrayIterator(new ArraySerializable(), $data, $object);

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

    public function testUsingStringForObjectName()
    {
        $data = [
            ['foo' => 'bar'],
        ];

        $hydratingIterator = new HydratingArrayIterator(new ArraySerializable(), $data, '\ArrayObject');

        $hydratingIterator->rewind();
        $this->assertEquals(new ArrayObject($data[0]), $hydratingIterator->current());
    }

    public function testThrowingInvalidArgumentExceptionWhenSettingPrototypeToInvalidClass()
    {
        $this->expectException(InvalidArgumentException::class);
        $hydratingIterator = new HydratingArrayIterator(new ArraySerializable(), [], 'not a real class');
    }
}
