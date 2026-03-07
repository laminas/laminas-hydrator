<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\Aggregate;

use ArrayObject;
use Laminas\Hydrator\Aggregate\AggregateHydrator;
use Laminas\Hydrator\Aggregate\ExtractEvent;
use Laminas\Hydrator\Aggregate\HydrateEvent;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorInterface;
use LaminasTest\Hydrator\TestAsset\AggregateObject;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(AggregateHydrator::class)]
final class AggregateHydratorFunctionalTest extends TestCase
{
    private AggregateHydrator $hydrator;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->hydrator = new AggregateHydrator();
    }

    /**
     * Verifies that no interaction happens when the aggregate hydrator is empty
     */
    public function testEmptyAggregate(): void
    {
        $object = new ArrayObject(['zaphod' => 'beeblebrox']);

        $this->assertSame([], $this->hydrator->extract($object));
        $this->assertSame($object, $this->hydrator->hydrate(['arthur' => 'dent'], $object));

        $this->assertSame(['zaphod' => 'beeblebrox'], $object->getArrayCopy());
    }

    /**
     * Verifies that using a single hydrator will have the aggregate hydrator behave like that single hydrator
     */
    #[DataProvider('getHydratorSet')]
    public function testSingleHydratorExtraction(HydratorInterface $comparisonHydrator, object $object): void
    {
        $blueprint = clone $object;

        $this->hydrator->add($comparisonHydrator);

        $this->assertSame($comparisonHydrator->extract($blueprint), $this->hydrator->extract($object));
    }

    /**
     * Verifies that using a single hydrator will have the aggregate hydrator behave like that single hydrator
     */
    #[DataProvider('getHydratorSet')]
    public function testSingleHydratorHydration(
        HydratorInterface $comparisonHydrator,
        object $object,
        array $data
    ): void {
        $blueprint = clone $object;

        $this->hydrator->add($comparisonHydrator);

        $hydratedBlueprint = $comparisonHydrator->hydrate($data, $blueprint);
        $hydrated          = $this->hydrator->hydrate($data, $object);

        $this->assertEquals($hydratedBlueprint, $hydrated);

        if ($hydratedBlueprint === $blueprint) {
            $this->assertSame($hydrated, $object);
        }
    }

    /**
     * Verifies that multiple hydrators in an aggregate merge the extracted data
     */
    public function testExtractWithMultipleHydrators(): void
    {
        $this->hydrator->add(new ClassMethodsHydrator());
        $this->hydrator->add(new ArraySerializableHydrator());

        $object = new AggregateObject();

        $extracted = $this->hydrator->extract($object);

        $this->assertArrayHasKey('maintainer', $extracted);
        $this->assertArrayHasKey('president', $extracted);
        $this->assertSame('Marvin', $extracted['maintainer']);
        $this->assertSame('Zaphod', $extracted['president']);
    }

    /**
     * Verifies that multiple hydrators in an aggregate merge the extracted data
     */
    public function testHydrateWithMultipleHydrators(): void
    {
        $this->hydrator->add(new ClassMethodsHydrator());
        $this->hydrator->add(new ArraySerializableHydrator());

        $object = new AggregateObject();

        $this->assertSame(
            $object,
            $this->hydrator->hydrate(['maintainer' => 'Trillian', 'president' => '???'], $object)
        );

        $this->assertArrayHasKey('maintainer', $object->arrayData);
        $this->assertArrayHasKey('president', $object->arrayData);
        $this->assertSame('Trillian', $object->arrayData['maintainer']);
        $this->assertSame('???', $object->arrayData['president']);
        $this->assertSame('Trillian', $object->maintainer);
    }

    /**
     * Verifies that stopping propagation within a listener in the hydrator allows modifying how the
     * hydrator behaves
     */
    public function testStoppedPropagationInExtraction(): void
    {
        $object   = new ArrayObject(['president' => 'Zaphod']);
        $callback = static function (ExtractEvent $event): void {
            $event->setExtractedData(['Ravenous Bugblatter Beast of Traal']);
            $event->stopPropagation();
        };

        $this->hydrator->add(new ArraySerializableHydrator());
        $this->hydrator->getEventManager()->attach(ExtractEvent::EVENT_EXTRACT, $callback, 1000);

        $this->assertSame(['Ravenous Bugblatter Beast of Traal'], $this->hydrator->extract($object));
    }

    /**
     * Verifies that stopping propagation within a listener in the hydrator allows modifying how the
     * hydrator behaves
     */
    public function testStoppedPropagationInHydration(): void
    {
        $object        = new ArrayObject();
        $swappedObject = new stdClass();
        $callback      = static function (HydrateEvent $event) use ($swappedObject): void {
            $event->setHydratedObject($swappedObject);
            $event->stopPropagation();
        };

        $this->hydrator->add(new ArraySerializableHydrator());
        $this->hydrator->getEventManager()->attach(HydrateEvent::EVENT_HYDRATE, $callback, 1000);

        $this->assertSame($swappedObject, $this->hydrator->hydrate(['president' => 'Zaphod'], $object));
    }

    /**
     * Data provider method
     *
     * @return list<array{0: HydratorInterface, 1: object, 2: array}>
     */
    public static function getHydratorSet(): array
    {
        return [
            [new ArraySerializableHydrator(), new ArrayObject(['zaphod' => 'beeblebrox']), ['arthur' => 'dent']],
        ];
    }
}
