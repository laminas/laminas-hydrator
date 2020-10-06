<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use ArrayObject;
use Interop\Container\ContainerInterface;
use Laminas\Hydrator\DelegatingHydrator;
use Laminas\Hydrator\HydratorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see DelegatingHydrator}
 *
 * @covers \Laminas\Hydrator\DelegatingHydrator
 */
class DelegatingHydratorTest extends TestCase
{
    /**
     * @var DelegatingHydrator
     */
    protected $hydrator;

    /**
     * @var ContainerInterface|\PHPUnit\Framework\MockObject\MockObject
     * @psalm-var ContainerInterface&\PHPUnit\Framework\MockObject\MockObject
     */
    protected $hydrators;

    /**
     * @var ArrayObject
     */
    protected $object;

    /**
     * {@inheritDoc}
     */
    protected function setUp() : void
    {
        $this->hydrators = $this->createMock(ContainerInterface::class);
        $this->hydrator = new DelegatingHydrator($this->hydrators);
        $this->object = new ArrayObject;
    }

    public function testExtract()
    {
        $hydrator = $this->createMock(HydratorInterface::class);
        $hydrator->expects($this->once())->method('extract')->with($this->object)->willReturn(['foo' => 'bar']);

        $this->hydrators
            ->expects($this->once())
            ->method('get')
            ->with(ArrayObject::class)
            ->willReturn($hydrator);

        $this->assertEquals(['foo' => 'bar'], $this->hydrator->extract($this->object));
    }

    public function testHydrate()
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
