<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ObjectPropertyHydrator;
use PHPUnit\Framework\TestCase;

class HydratorObjectPropertyTest extends TestCase
{
    private ObjectPropertyHydrator $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new ObjectPropertyHydrator();
    }

    public function testMultipleInvocationsWithDifferentFiltersFindsAllProperties(): void
    {
        $instance = new class {
            /** @var int */
            public $id;
            /** @var int[] */
            public $array;
            /** @var object{id:int} */
            public $object;

            public function __construct()
            {
                $this->id     = 4;
                $this->array  = [4, 3, 5, 6];
                $this->object = new class {
                    /** @var int */
                    public $id = 4;
                };
            }
        };

        $this->hydrator->addFilter('values', static fn(): bool => true);
        $result = $this->hydrator->extract($instance);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals($instance->id, $result['id']);
        $this->assertArrayHasKey('array', $result);
        $this->assertEquals($instance->array, $result['array']);
        $this->assertArrayHasKey('object', $result);
        $this->assertSame($instance->object, $result['object']);

        $this->hydrator->removeFilter('values');
        $this->hydrator->addFilter('complex', static fn($property): bool => match ($property) {
            'array', 'object' => false,
            default => true,
        });
        $result = $this->hydrator->extract($instance);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals($instance->id, $result['id']);
        $this->assertArrayNotHasKey('array', $result);
        $this->assertArrayNotHasKey('object', $result);
    }
}
