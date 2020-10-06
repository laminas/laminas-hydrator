<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\Aggregate;

use Laminas\Hydrator\Aggregate\HydrateEvent;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Unit tests for {@see HydrateEvent}
 */
class HydrateEventTest extends TestCase
{
    /**
     * @covers \Laminas\Hydrator\Aggregate\HydrateEvent
     *
     * @return void
     */
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
