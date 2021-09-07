<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\AbstractHydrator;
use Laminas\Hydrator\HydratorAwareTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers Laminas\Hydrator\HydratorAwareTrait
 */
class HydratorAwareTraitTest extends TestCase
{
    public function testSetHydrator(): void
    {
        /** @psalm-suppress InvalidScalarArgument False positive */
        $object = $this->getObjectForTrait(HydratorAwareTrait::class);

        $this->assertSame(null, $object->getHydrator());

        $hydrator = $this->getMockForAbstractClass(AbstractHydrator::class);

        $object->setHydrator($hydrator);

        $this->assertSame($hydrator, $object->getHydrator());
    }

    public function testGetHydrator(): void
    {
        /** @psalm-suppress InvalidScalarArgument False positive */
        $object = $this->getObjectForTrait(HydratorAwareTrait::class);

        $this->assertNull($object->getHydrator());

        $hydrator = $this->getMockForAbstractClass(AbstractHydrator::class);

        $object->setHydrator($hydrator);

        $this->assertEquals($hydrator, $object->getHydrator());
    }
}
