<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator;

use InvalidArgumentException;
use Laminas\Hydrator\Reflection;
use stdClass;

/**
 * Unit tests for {@see Reflection}
 *
 * @covers \Laminas\Hydrator\Reflection
 */
class ReflectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reflection
     */
    protected $hydrator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->hydrator = new Reflection();
    }

    public function testCanExtract()
    {
        $this->assertSame([], $this->hydrator->extract(new stdClass()));
    }

    public function testCanHydrate()
    {
        $object = new stdClass();

        $this->assertSame($object, $this->hydrator->hydrate(['foo' => 'bar'], $object));
    }

    public function testNotStringOrObjectOnExtract()
    {
        $this->setExpectedException(InvalidArgumentException::class, 'Input must be a string or an object.');

        $argument = (int) 1;
        $this->hydrator->extract($argument);
    }

    public function testNotStringOrObjectOnHydrate()
    {
        $this->setExpectedException(InvalidArgumentException::class, 'Input must be a string or an object.');

        $argument = (int) 1;
        $this->hydrator->hydrate([ 'foo' => 'bar' ], $argument);
    }
}
