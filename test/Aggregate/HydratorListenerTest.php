<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\Aggregate;

use Laminas\EventManager\EventManagerInterface;
use Laminas\Hydrator\Aggregate\ExtractEvent;
use Laminas\Hydrator\Aggregate\HydrateEvent;
use Laminas\Hydrator\Aggregate\HydratorListener;
use Laminas\Hydrator\HydratorInterface;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Unit tests for {@see HydratorListener}
 */
class HydratorListenerTest extends TestCase
{
    /**
     * @var HydratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $hydrator;

    /**
     * @var HydratorListener
     */
    protected $listener;

    /**
     * {@inheritDoc}
     *
     * @covers \Laminas\Hydrator\Aggregate\HydratorListener::__construct
     */
    protected function setUp() : void
    {
        $this->hydrator = $this->createMock(HydratorInterface::class);
        $this->listener = new HydratorListener($this->hydrator);
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\HydratorListener::attach
     */
    public function testAttach()
    {
        $eventManager = $this->createMock(EventManagerInterface::class);

        $eventManager
            ->expects($this->exactly(2))
            ->method('attach')
            ->with(
                $this->logicalOr(HydrateEvent::EVENT_HYDRATE, ExtractEvent::EVENT_EXTRACT),
                $this->logicalAnd(
                    $this->callback('is_callable'),
                    $this->logicalOr([$this->listener, 'onHydrate'], [$this->listener, 'onExtract'])
                )
            );

        $this->listener->attach($eventManager);
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\HydratorListener::onHydrate
     */
    public function testOnHydrate()
    {
        $object   = new stdClass();
        $hydrated = new stdClass();
        $data     = ['foo' => 'bar'];
        $event    = $this
            ->getMockBuilder(HydrateEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())->method('getHydratedObject')->will($this->returnValue($object));
        $event->expects($this->any())->method('getHydrationData')->will($this->returnValue($data));

        $this
            ->hydrator
            ->expects($this->once())
            ->method('hydrate')
            ->with($data, $object)
            ->will($this->returnValue($hydrated));
        $event->expects($this->once())->method('setHydratedObject')->with($hydrated);

        $this->assertSame($hydrated, $this->listener->onHydrate($event));
    }

    /**
     * @covers \Laminas\Hydrator\Aggregate\HydratorListener::onExtract
     */
    public function testOnExtract()
    {
        $object = new stdClass();
        $data   = ['foo' => 'bar'];
        $event  = $this
            ->getMockBuilder(ExtractEvent::class)
            ->disableOriginalConstructor()
            ->getMock();


        $event->expects($this->any())->method('getExtractionObject')->will($this->returnValue($object));

        $this
            ->hydrator
            ->expects($this->once())
            ->method('extract')
            ->with($object)
            ->will($this->returnValue($data));
        $event->expects($this->once())->method('mergeExtractedData')->with($data);

        $this->assertSame($data, $this->listener->onExtract($event));
    }
}
