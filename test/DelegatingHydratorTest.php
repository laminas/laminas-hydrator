<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator;

use ArrayObject;
use Laminas\Hydrator\DelegatingHydrator;

/**
 * Unit tests for {@see DelegatingHydrator}
 *
 * @covers \Laminas\Hydrator\DelegatingHydrator
 */
class DelegatingHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DelegatingHydrator
     */
    protected $hydrator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $hydrators;

    /**
     * @var ArrayObject
     */
    protected $object;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->hydrators = $this->getMock('Laminas\ServiceManager\ServiceLocatorInterface');
        $this->hydrator = new DelegatingHydrator($this->hydrators);
        $this->object = new ArrayObject;
    }

    public function testExtract()
    {
        $this->hydrators->expects($this->any())
            ->method('has')
            ->with('ArrayObject')
            ->will($this->returnValue(true));

        $hydrator = $this->getMock('Laminas\Hydrator\HydratorInterface');

        $this->hydrators->expects($this->any())
            ->method('get')
            ->with('ArrayObject')
            ->will($this->returnValue($hydrator));

        $hydrator->expects($this->any())
            ->method('extract')
            ->with($this->object)
            ->will($this->returnValue(['foo' => 'bar']));

        $this->assertEquals(['foo' => 'bar'], $hydrator->extract($this->object));
    }

    public function testHydrate()
    {
        $this->hydrators->expects($this->any())
            ->method('has')
            ->with('ArrayObject')
            ->will($this->returnValue(true));

        $hydrator = $this->getMock('Laminas\Hydrator\HydratorInterface');

        $this->hydrators->expects($this->any())
            ->method('get')
            ->with('ArrayObject')
            ->will($this->returnValue($hydrator));

        $hydrator->expects($this->any())
            ->method('hydrate')
            ->with(['foo' => 'bar'], $this->object)
            ->will($this->returnValue($this->object));
        $this->assertEquals($this->object, $hydrator->hydrate(['foo' => 'bar'], $this->object));
    }
}
