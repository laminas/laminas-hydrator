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
     * @var EventManager|\PHPUnit\Framework\MockObject\MockObject
     * @psalm-var EventManager&\PHPUnit\Framework\MockObject\MockObject
     */
    protected $eventManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp() : void
    {
        $this->eventManager = $this->createMock(EventManager::class);
        $this->hydrator     = new AggregateHydrator();

        $this->hydrator->setEventManager($this->eventManager);
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::add
     */
    public function testAdd()
    {
        $attached = $this->createMock(HydratorInterface::class);

        $this->eventManager
            ->expects($this->exactly(2))
            ->method('attach')
            ->withConsecutive(
                [HydrateEvent::EVENT_HYDRATE, $this->isType('callable'), 123],
                [ExtractEvent::EVENT_EXTRACT, $this->isType('callable'), 123],
            );

        $this->hydrator->add($attached, 123);
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::hydrate
     */
    public function testHydrate()
    {
        $object = new stdClass();

        $this->eventManager
            ->expects($this->once())
            ->method('triggerEvent')
            ->with($this->isInstanceOf(HydrateEvent::class));

        $this->assertSame($object, $this->hydrator->hydrate(['foo' => 'bar'], $object));
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::extract
     */
    public function testExtract()
    {
        $object = new stdClass();

        $this->eventManager
            ->expects($this->once())
            ->method('triggerEvent')
            ->with($this->isInstanceOf(ExtractEvent::class));

        $this->assertSame([], $this->hydrator->extract($object));
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::getEventManager
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::setEventManager
     */
    public function testGetSetManager()
    {
        $hydrator     = new AggregateHydrator();
        $eventManager = $this->createMock(EventManager::class);

        $this->assertInstanceOf(EventManager::class, $hydrator->getEventManager());

        $eventManager
            ->expects($this->once())
            ->method('setIdentifiers')
            ->with([AggregateHydrator::class, AggregateHydrator::class]);

        $hydrator->setEventManager($eventManager);

        $this->assertSame($eventManager, $hydrator->getEventManager());
    }
}
