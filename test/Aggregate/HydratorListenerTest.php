<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Aggregate;

use Laminas\EventManager\EventManagerInterface;
use Laminas\Hydrator\Aggregate\ExtractEvent;
use Laminas\Hydrator\Aggregate\HydrateEvent;
use Laminas\Hydrator\Aggregate\HydratorListener;
use Laminas\Hydrator\HydratorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(HydratorListener::class)]
final class HydratorListenerTest extends TestCase
{
    private HydratorInterface&MockObject $hydrator;
    private HydratorListener $listener;

    protected function setUp(): void
    {
        $this->hydrator = $this->createMock(HydratorInterface::class);
        $this->listener = new HydratorListener($this->hydrator);
    }

    public function testAttach(): void
    {
        $eventManager = $this->createMock(EventManagerInterface::class);

        $eventManager
            ->expects($this->exactly(2))
            ->method('attach')
            ->with(
                $this->logicalOr(HydrateEvent::EVENT_HYDRATE, ExtractEvent::EVENT_EXTRACT),
                $this->logicalAnd(
                    $this->callback('is_callable'),
                    $this->logicalOr($this->listener->onHydrate(...), $this->listener->onExtract(...))
                )
            );

        $this->listener->attach($eventManager);
    }

    public function testOnHydrate(): void
    {
        $object   = new stdClass();
        $hydrated = new stdClass();
        $data     = ['foo' => 'bar'];
        $event    = new HydrateEvent((object) [], (object) [], $data);

        $this
            ->hydrator
            ->expects($this->once())
            ->method('hydrate')
            ->with($data, $object)
            ->willReturn($hydrated);

        $this->assertSame($hydrated, $this->listener->onHydrate($event));
    }

    public function testOnExtract(): void
    {
        $object = new stdClass();
        $data   = ['foo' => 'bar'];
        $event  = new ExtractEvent((object) [], $object);

        $this
            ->hydrator
            ->expects($this->once())
            ->method('extract')
            ->with($object)
            ->willReturn($data);

        $this->assertSame($data, $this->listener->onExtract($event));
    }
}
