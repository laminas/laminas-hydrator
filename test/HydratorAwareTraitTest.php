<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use LaminasTest\Hydrator\TestAsset\HydratorAwareTraitImplementor;
use PHPUnit\Framework\TestCase;

class HydratorAwareTraitTest extends TestCase
{
    public function testSetHydrator(): void
    {
        $object = new HydratorAwareTraitImplementor();
        self::assertNull($object->getHydrator());
        $hydrator = $this->createMock(HydratorInterface::class);
        $object->setHydrator($hydrator);
        self::assertSame($hydrator, $object->getHydrator());
    }

    public function testGetHydrator(): void
    {
        $object = new HydratorAwareTraitImplementor();
        self::assertNull($object->getHydrator());
        $hydrator = $this->createMock(HydratorInterface::class);
        $object->setHydrator($hydrator);
        self::assertSame($hydrator, $object->getHydrator());
    }
}
