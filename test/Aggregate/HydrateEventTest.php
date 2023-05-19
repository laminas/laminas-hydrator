<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Aggregate;

use Laminas\Hydrator\Aggregate\HydrateEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(HydrateEvent::class)]
class HydrateEventTest extends TestCase
{
    public function testEvent(): void
    {
        $target    = new stdClass();
        $hydrated1 = new stdClass();
        $data1     = ['president' => 'Zaphod'];
        $event     = new HydrateEvent($target, $hydrated1, $data1);
        $data2     = ['maintainer' => 'Marvin'];
        $hydrated2 = new stdClass();

        $this->assertSame(HydrateEvent::EVENT_HYDRATE, $event->getName());
        $this->assertSame($target, $event->getTarget());
        $this->assertSame($hydrated1, $event->getHydratedObject());
        $this->assertSame($data1, $event->getHydrationData());

        $event->setHydrationData($data2);

        $this->assertSame($data2, $event->getHydrationData());

        $event->setHydratedObject($hydrated2);

        $this->assertSame($hydrated2, $event->getHydratedObject());
    }
}
