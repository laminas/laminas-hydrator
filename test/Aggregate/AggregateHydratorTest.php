<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\Aggregate;

use Laminas\EventManager\EventManager;
use Laminas\Hydrator\Aggregate\AggregateHydrator;
use Laminas\Hydrator\Aggregate\ExtractEvent;
use Laminas\Hydrator\Aggregate\HydrateEvent;
use Laminas\Hydrator\HydratorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use stdClass;

/**
 * Unit tests for {@see AggregateHydrator}
 */
class AggregateHydratorTest extends TestCase
{
    /**
     * @var AggregateHydrator
     */
    protected $hydrator;

    /**
     * @var \Laminas\EventManager\EventManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->eventManager = $this->prophesize(EventManager::class);
        $this->hydrator     = new AggregateHydrator();

        $this->hydrator->setEventManager($this->eventManager->reveal());
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::add
     */
    public function testAdd()
    {
        $attached = $this->prophesize(HydratorInterface::class);

        $this->eventManager
            ->attach(HydrateEvent::EVENT_HYDRATE, Argument::type('callable'), 123)
            ->shouldBeCalled();
        $this->eventManager
            ->attach(ExtractEvent::EVENT_EXTRACT, Argument::type('callable'), 123)
            ->shouldBeCalled();

        $this->hydrator->add($attached->reveal(), 123);
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::hydrate
     */
    public function testHydrate()
    {
        $object = new stdClass();

        $this->eventManager
            ->triggerEvent(Argument::type(HydrateEvent::class))
            ->shouldBeCalled();

        $this->assertSame($object, $this->hydrator->hydrate(['foo' => 'bar'], $object));
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::extract
     */
    public function testExtract()
    {
        $object = new stdClass();

        $this->eventManager
            ->triggerEvent(Argument::type(ExtractEvent::class))
            ->shouldBeCalled();

        $this->assertSame([], $this->hydrator->extract($object));
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::getEventManager
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::setEventManager
     */
    public function testGetSetManager()
    {
        $hydrator     = new AggregateHydrator();
        $eventManager = $this->prophesize(EventManager::class);

        $this->assertInstanceOf(EventManager::class, $hydrator->getEventManager());

        $eventManager
            ->setIdentifiers([AggregateHydrator::class, AggregateHydrator::class])
            ->shouldBeCalled();

        $hydrator->setEventManager($eventManager->reveal());

        $this->assertSame($eventManager->reveal(), $hydrator->getEventManager());
    }
}
