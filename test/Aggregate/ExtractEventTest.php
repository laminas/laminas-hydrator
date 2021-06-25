<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Aggregate;

use Laminas\Hydrator\Aggregate\ExtractEvent;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Unit tests for {@see ExtractEvent}
 */
class ExtractEventTest extends TestCase
{
    /**
     * @covers \Laminas\Hydrator\Aggregate\ExtractEvent
     */
    public function testEvent(): void
    {
        $target  = new stdClass();
        $object1 = new stdClass();
        $event   = new ExtractEvent($target, $object1);
        $data2   = ['maintainer' => 'Marvin'];
        $object2 = new stdClass();

        $this->assertSame(ExtractEvent::EVENT_EXTRACT, $event->getName());
        $this->assertSame($target, $event->getTarget());
        $this->assertSame($object1, $event->getExtractionObject());
        $this->assertSame([], $event->getExtractedData());

        $event->setExtractedData($data2);

        $this->assertSame($data2, $event->getExtractedData());

        $event->setExtractionObject($object2);

        $this->assertSame($object2, $event->getExtractionObject());

        $event->mergeExtractedData(['president' => 'Zaphod']);

        $extracted = $event->getExtractedData();

        $this->assertCount(2, $extracted);
        $this->assertSame('Marvin', $extracted['maintainer']);
        $this->assertSame('Zaphod', $extracted['president']);
    }
}
