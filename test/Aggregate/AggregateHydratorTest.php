<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Aggregate;

use Laminas\EventManager\EventManager;
use Laminas\Hydrator\Aggregate\AggregateHydrator;
use Laminas\Hydrator\Aggregate\ExtractEvent;
use Laminas\Hydrator\Aggregate\HydrateEvent;
use Laminas\Hydrator\HydratorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(AggregateHydrator::class)]
final class AggregateHydratorTest extends TestCase
{
    private AggregateHydrator $hydrator;
    private EventManager&MockObject $eventManager;

    protected function setUp(): void
    {
        $this->eventManager = $this->createMock(EventManager::class);
        $this->hydrator     = new AggregateHydrator();

        $this->hydrator->setEventManager($this->eventManager);
    }

    public function testAdd(): void
    {
        $attached = $this->createMock(HydratorInterface::class);

        $this->eventManager
            ->expects($this->exactly(2))
            ->method('attach')
            ->with(
                self::callback(function (mixed $event): bool {
                    $this->assertIsString($event);
                    $this->assertContains($event, [
                        HydrateEvent::EVENT_HYDRATE,
                        ExtractEvent::EVENT_EXTRACT,
                    ]);

                    return true;
                }),
                self::callback(function (mixed $listener): bool {
                    $this->assertIsCallable($listener);

                    return true;
                }),
                self::callback(function (mixed $priority): bool {
                    $this->assertSame(123, $priority);

                    return true;
                }),
            );

        $this->hydrator->add($attached, 123);
    }

    public function testHydrate(): void
    {
        $object = new stdClass();

        $this->eventManager
            ->expects($this->once())
            ->method('triggerEvent')
            ->with($this->isInstanceOf(HydrateEvent::class));

        $this->assertSame($object, $this->hydrator->hydrate(['foo' => 'bar'], $object));
    }

    public function testExtract(): void
    {
        $object = new stdClass();

        $this->eventManager
            ->expects($this->once())
            ->method('triggerEvent')
            ->with($this->isInstanceOf(ExtractEvent::class));

        $this->assertSame([], $this->hydrator->extract($object));
    }

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
