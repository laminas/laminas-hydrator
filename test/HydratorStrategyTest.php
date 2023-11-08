<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\Strategy\StrategyInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HydratorStrategyTest extends TestCase
{
    /**
     * The hydrator that is used during testing.
     */
    private HydratorInterface $hydrator;

    protected function setUp(): void
    {
        $this->hydrator = new ClassMethodsHydrator();
    }

    public function testAddingStrategy(): void
    {
        $this->assertFalse($this->hydrator->hasStrategy('myStrategy'));

        $this->hydrator->addStrategy('myStrategy', new TestAsset\HydratorStrategy());

        $this->assertTrue($this->hydrator->hasStrategy('myStrategy'));
    }

    public function testCheckStrategyEmpty(): void
    {
        $this->assertFalse($this->hydrator->hasStrategy('myStrategy'));
    }

    public function testCheckStrategyNotEmpty(): void
    {
        $this->hydrator->addStrategy('myStrategy', new TestAsset\HydratorStrategy());

        $this->assertTrue($this->hydrator->hasStrategy('myStrategy'));
    }

    public function testRemovingStrategy(): void
    {
        $this->assertFalse($this->hydrator->hasStrategy('myStrategy'));

        $this->hydrator->addStrategy('myStrategy', new TestAsset\HydratorStrategy());
        $this->assertTrue($this->hydrator->hasStrategy('myStrategy'));

        $this->hydrator->removeStrategy('myStrategy');
        $this->assertFalse($this->hydrator->hasStrategy('myStrategy'));
    }

    public function testRetrieveStrategy(): void
    {
        $strategy = new TestAsset\HydratorStrategy();
        $this->hydrator->addStrategy('myStrategy', $strategy);

        $this->assertEquals($strategy, $this->hydrator->getStrategy('myStrategy'));
    }

    public function testExtractingObjects(): void
    {
        $this->hydrator->addStrategy('entities', new TestAsset\HydratorStrategy());

        $entityA = new TestAsset\HydratorStrategyEntityA();
        $entityA->addEntity(new TestAsset\HydratorStrategyEntityB(111, 'AAA'));
        $entityA->addEntity(new TestAsset\HydratorStrategyEntityB(222, 'BBB'));

        $attributes = $this->hydrator->extract($entityA);

        $this->assertContains(111, $attributes['entities']);
        $this->assertContains(222, $attributes['entities']);
    }

    public function testHydratingObjects(): void
    {
        $this->hydrator->addStrategy('entities', new TestAsset\HydratorStrategy());

        $entityA = new TestAsset\HydratorStrategyEntityA();
        $entityA->addEntity(new TestAsset\HydratorStrategyEntityB(111, 'AAA'));
        $entityA->addEntity(new TestAsset\HydratorStrategyEntityB(222, 'BBB'));

        $attributes               = $this->hydrator->extract($entityA);
        $attributes['entities'][] = 333;

        $this->hydrator->hydrate($attributes, $entityA);
        $entities = $entityA->getEntities();

        $this->assertCount(3, $entities);
    }

    #[DataProvider('underscoreHandlingDataProvider')]
    public function testWhenUsingUnderscoreSeparatedKeysHydratorStrategyIsAlwaysConsideredUnderscoreSeparatedToo(
        bool $underscoreSeparatedKeys,
        string $formFieldKey
    ): void {
        $hydrator = new ClassMethodsHydrator($underscoreSeparatedKeys);

        $strategy = $this->createMock(StrategyInterface::class);

        $entity = new TestAsset\ClassMethodsUnderscore();
        $value  = $entity->getFooBar();

        $hydrator->addStrategy($formFieldKey, $strategy);

        $strategy
            ->expects($this->once())
            ->method('extract')
            ->with($this->identicalTo($value))
            ->willReturn($value);

        $attributes = $hydrator->extract($entity);

        $strategy
            ->expects($this->once())
            ->method('hydrate')
            ->with($this->identicalTo($value))
            ->willReturn($value);

        $hydrator->hydrate($attributes, $entity);
    }

    /**
     * @psalm-return array<array-key, array{
     *     0: bool,
     *     1: string
     * }>
     */
    public static function underscoreHandlingDataProvider(): array
    {
        return [
            [true, 'foo_bar'],
            [false, 'fooBar'],
        ];
    }

    public function testContextAwarenessExtract(): void
    {
        $strategy = new TestAsset\HydratorStrategyContextAware();
        $this->hydrator->addStrategy('field2', $strategy);

        $entityB = new TestAsset\HydratorStrategyEntityB('X', 'Y');
        $this->hydrator->extract($entityB);

        $this->assertEquals($entityB, $strategy->object);
    }

    public function testContextAwarenessHydrate(): void
    {
        $strategy = new TestAsset\HydratorStrategyContextAware();
        $this->hydrator->addStrategy('field2', $strategy);

        $entityB = new TestAsset\HydratorStrategyEntityB('X', 'Y');
        $data    = ['field1' => 'A', 'field2' => 'B'];
        $this->hydrator->hydrate($data, $entityB);

        $this->assertEquals($data, $strategy->data);
    }
}
