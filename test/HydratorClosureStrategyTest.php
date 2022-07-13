<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\Hydrator\Strategy\ClosureStrategy;
use PHPUnit\Framework\TestCase;

use function sprintf;

class HydratorClosureStrategyTest extends TestCase
{
    /**
     * The hydrator that is used during testing.
     */
    private HydratorInterface $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new ObjectPropertyHydrator();
    }

    public function testAddingStrategy(): void
    {
        $this->assertFalse($this->hydrator->hasStrategy('myStrategy'));

        $this->hydrator->addStrategy('myStrategy', new ClosureStrategy());

        $this->assertTrue($this->hydrator->hasStrategy('myStrategy'));
    }

    public function testCheckStrategyEmpty(): void
    {
        $this->assertFalse($this->hydrator->hasStrategy('myStrategy'));
    }

    public function testCheckStrategyNotEmpty(): void
    {
        $this->hydrator->addStrategy('myStrategy', new ClosureStrategy());

        $this->assertTrue($this->hydrator->hasStrategy('myStrategy'));
    }

    public function testRemovingStrategy(): void
    {
        $this->assertFalse($this->hydrator->hasStrategy('myStrategy'));

        $this->hydrator->addStrategy('myStrategy', new ClosureStrategy());
        $this->assertTrue($this->hydrator->hasStrategy('myStrategy'));

        $this->hydrator->removeStrategy('myStrategy');
        $this->assertFalse($this->hydrator->hasStrategy('myStrategy'));
    }

    public function testRetrieveStrategy(): void
    {
        $strategy = new ClosureStrategy();
        $this->hydrator->addStrategy('myStrategy', $strategy);

        $this->assertEquals($strategy, $this->hydrator->getStrategy('myStrategy'));
    }

    public function testExtractingObjects(): void
    {
        $this->hydrator->addStrategy('field1', new ClosureStrategy(
            static fn($value) => sprintf('%s', $value),
            null
        ));
        $this->hydrator->addStrategy('field2', new ClosureStrategy(
            static fn($value) => sprintf('hello, %s!', $value),
            null
        ));

        $entity = new TestAsset\HydratorClosureStrategyEntity(111, 'world');
        $values = $this->hydrator->extract($entity);

        $this->assertEquals(111, $values['field1']);
        $this->assertEquals('hello, world!', $values['field2']);
    }

    public function testHydratingObjects(): void
    {
        $this->hydrator->addStrategy('field2', new ClosureStrategy(
            null,
            static fn($value) => sprintf('hello, %s!', $value)
        ));
        $this->hydrator->addStrategy('field3', new ClosureStrategy(
            null,
            static fn($value) => new TestAsset\HydratorClosureStrategyEntity($value, sprintf('111%s', $value))
        ));

        $entity = new TestAsset\HydratorClosureStrategyEntity(111, 'world');

        $values           = $this->hydrator->extract($entity);
        $values['field3'] = 333;

        $this->assertCount(2, (array) $entity);
        $this->hydrator->hydrate($values, $entity);
        $this->assertCount(3, (array) $entity);

        $this->assertInstanceOf(TestAsset\HydratorClosureStrategyEntity::class, $entity->field3);
    }
}
