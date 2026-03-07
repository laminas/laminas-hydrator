<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use ArrayObject;
use Laminas\Hydrator\DelegatingHydrator;
use Laminas\Hydrator\HydratorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(DelegatingHydrator::class)]
final class DelegatingHydratorTest extends TestCase
{
    private DelegatingHydrator $hydrator;

    private ContainerInterface&MockObject $hydrators;

    private ArrayObject $object;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->hydrators = $this->createMock(ContainerInterface::class);
        $this->hydrator  = new DelegatingHydrator($this->hydrators);
        $this->object    = new ArrayObject();
    }

    public function testExtract(): void
    {
        $hydrator = $this->createMock(HydratorInterface::class);
        $hydrator->expects($this->once())->method('extract')->with($this->object)->willReturn(['foo' => 'bar']);

        $this->hydrators
            ->expects($this->once())
            ->method('get')
            ->with(ArrayObject::class)
            ->willReturn($hydrator);

        $this->assertSame(['foo' => 'bar'], $this->hydrator->extract($this->object));
    }

    public function testHydrate(): void
    {
        $hydrator = $this->createMock(HydratorInterface::class);
        $hydrator->expects($this->once())->method('hydrate')->with(['foo' => 'bar'])->willReturn($this->object);

        $this->hydrators
            ->expects($this->once())
            ->method('get')
            ->with(ArrayObject::class)
            ->willReturn($hydrator);

        $this->assertEquals($this->object, $this->hydrator->hydrate(['foo' => 'bar'], $this->object));
    }
}
