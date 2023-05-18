<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Aggregate;

use Laminas\EventManager\EventManager;
use Laminas\Hydrator\Aggregate\AggregateHydrator;
use Laminas\Hydrator\Aggregate\ExtractEvent;
use Laminas\Hydrator\Aggregate\HydrateEvent;
use Laminas\Hydrator\HydratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Unit tests for {@see AggregateHydrator}
 */
class AggregateHydratorTest extends TestCase
{
    /** @var AggregateHydrator */
    protected $hydrator;

    /** @var EventManager&MockObject */
    protected EventManager $eventManager;

    protected function setUp(): void
    {
        $this->eventManager = $this->createMock(EventManager::class);
        $this->hydrator     = new AggregateHydrator();

        $this->hydrator->setEventManager($this->eventManager);
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::add
     */
    public function testAdd(): void
    {
        $attached = $this->createMock(HydratorInterface::class);

        $this->eventManager
            ->expects(self::exactly(2))
            ->method('attach')
            ->with(
                self::callback(function (mixed $event): bool {
                    self::assertIsString($event);
                    self::assertContains($event, [
                        HydrateEvent::EVENT_HYDRATE,
                        ExtractEvent::EVENT_EXTRACT,
                    ]);

                    return true;
                }),
                self::callback(function (mixed $listener): bool {
                    self::assertIsCallable($listener);

                    return true;
                }),
                self::callback(function (mixed $priority): bool {
                    self::assertSame(123, $priority);

                    return true;
                }),
            );

        $this->hydrator->add($attached, 123);
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\AggregateHydrator::hydrate
     */
    public function testHydrate(): void
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
    public function testExtract(): void
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
    public function testGetSetManager(): void
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
