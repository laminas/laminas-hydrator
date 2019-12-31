<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\Aggregate;

use Laminas\Hydrator\Aggregate\AggregateHydrator;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * Unit tests for {@see AggregateHydrator}
 */
class AggregateHydratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AggregateHydrator
     */
    protected $hydrator;

    /**
     * @var \Laminas\EventManager\EventManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->eventManager = $this->getMock('Laminas\EventManager\EventManagerInterface');
        $this->hydrator     = new AggregateHydrator();

        $this->hydrator->setEventManager($this->eventManager);
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::add
     */
    public function testAdd()
    {
        $attached = $this->getMock('Laminas\Hydrator\HydratorInterface');

        $this
            ->eventManager
            ->expects($this->once())
            ->method('attachAggregate')
            ->with($this->isInstanceOf('Laminas\Hydrator\Aggregate\HydratorListener'), 123);

        $this->hydrator->add($attached, 123);
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::hydrate
     */
    public function testHydrate()
    {
        $object = new stdClass();

        $this
            ->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with($this->isInstanceOf('Laminas\Hydrator\Aggregate\HydrateEvent'));

        $this->assertSame($object, $this->hydrator->hydrate(['foo' => 'bar'], $object));
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::extract
     */
    public function testExtract()
    {
        $object = new stdClass();

        $this
            ->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with($this->isInstanceOf('Laminas\Hydrator\Aggregate\ExtractEvent'));

        $this->assertSame([], $this->hydrator->extract($object));
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::getEventManager
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::setEventManager
     */
    public function testGetSetManager()
    {
        $hydrator     = new AggregateHydrator();
        $eventManager = $this->getMock('Laminas\EventManager\EventManagerInterface');

        $this->assertInstanceOf('Laminas\EventManager\EventManagerInterface', $hydrator->getEventManager());

        $eventManager
            ->expects($this->once())
            ->method('setIdentifiers')
            ->with(
                [
                     'Laminas\Hydrator\Aggregate\AggregateHydrator',
                     'Laminas\Hydrator\Aggregate\AggregateHydrator',
                ]
            );

        $hydrator->setEventManager($eventManager);

        $this->assertSame($eventManager, $hydrator->getEventManager());
    }
}
