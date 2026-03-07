<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use ArrayObject;
use Laminas\Hydrator\ArraySerializableHydrator;
use LaminasTest\Hydrator\TestAsset\ArraySerializable as ArraySerializableAsset;
use LaminasTest\Hydrator\TestAsset\ArraySerializableNoGetArrayCopy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use TypeError;

use function array_merge;

#[CoversClass(ArraySerializableHydrator::class)]
final class ArraySerializableHydratorTest extends TestCase
{
    use HydratorTestTrait;

    protected ArraySerializableHydrator $hydrator;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->hydrator = new ArraySerializableHydrator();
    }

    /**
     * Verify that we get an exception when trying to extract on a non-object
     */
    public function testHydratorExtractThrowsExceptionOnNonObjectParameter(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('object');
        $this->hydrator->extract('thisIsNotAnObject');
    }

    /**
     * Verify that we get an exception when trying to hydrate a non-object
     */
    public function testHydratorHydrateThrowsExceptionOnNonObjectParameter(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('object');
        $this->hydrator->hydrate(['some' => 'data'], 'thisIsNotAnObject');
    }

    /**
     * Verifies that we can extract from an ArraySerializableInterface
     */
    public function testCanExtractFromArraySerializableObject(): void
    {
        $this->assertSame(
            [
                'foo'   => 'bar',
                'bar'   => 'foo',
                'blubb' => 'baz',
                'quo'   => 'blubb',
            ],
            $this->hydrator->extract(new ArraySerializableAsset())
        );
    }

    /**
     * Verifies we can hydrate an ArraySerializableInterface
     */
    public function testCanHydrateToArraySerializableObject(): void
    {
        $data   = [
            'foo'   => 'bar1',
            'bar'   => 'foo1',
            'blubb' => 'baz1',
            'quo'   => 'blubb1',
        ];
        $object = $this->hydrator->hydrate($data, new ArraySerializableAsset());

        $this->assertSame($data, $object->getArrayCopy());
    }

    /**
     * Verifies that when an object already has properties,
     * these properties are preserved when it's hydrated with new data
     * existing properties should get overwritten
     */
    #[Group('65')]
    public function testWillPreserveOriginalPropsAtHydration(): void
    {
        $original = new ArraySerializableAsset();

        $data = [
            'bar' => 'foo1',
        ];

        $expected = array_merge($original->getArrayCopy(), $data);

        $actual = $this->hydrator->hydrate($data, $original);

        $this->assertSame($expected, $actual->getArrayCopy());
    }

    /**
     * To preserve backwards compatibility, if getArrayCopy() is not implemented
     * by the to-be hydrated object, simply exchange the array
     */
    #[Group('65')]
    public function testWillReplaceArrayIfNoGetArrayCopy(): void
    {
        $original = new ArraySerializableNoGetArrayCopy();

        $data = [
            'bar' => 'foo1',
        ];

        $expected = $data;

        $actual = $this->hydrator->hydrate($data, $original);
        $this->assertSame($expected, $actual->getData());
    }

    /**
     * @return string[][][]
     * @psalm-return array<string, array{0: string[], 1: string[], 2: string[]}>
     */
    public static function arrayDataProvider(): array
    {
        // @codingStandardsIgnoreStart
        return [
            //               [ existing data,  submitted data,                   expected ]
            'empty'       => [['what-exists'], [],                               []],
            'replacement' => [['what-exists'], ['laminas-hydrator', 'laminas-stdlib'], ['laminas-hydrator', 'laminas-stdlib']],
            'partial'     => [['what-exists'], ['what-exists', 'laminas-hydrator'], ['what-exists', 'laminas-hydrator']],
        ];
        // @codingStandardsIgnoreEnd
    }

    /**
     * #65 ensures that hydration will merge data into the existing object.
     * However, #66 notes that there's an issue with this when it comes to data
     * representing arrays: if the original array had data, but the submitted
     * one _removes_ data, then no change occurs. Ideally, in these cases, the
     * submitted value should _replace_ the original.
     *
     * @param string[] $start
     * @param string[] $submit
     * @param string[] $expected
     */
    #[DataProvider('arrayDataProvider')]
    #[Group('66')]
    public function testHydrationWillReplaceNestedArrayData(
        array $start,
        array $submit,
        array $expected
    ): void {
        $original = new ArraySerializableAsset();
        $original->exchangeArray([
            'tags' => $start,
        ]);

        $data = ['tags' => $submit];

        $this->hydrator->hydrate($data, $original);

        $final = $original->getArrayCopy();

        $this->assertSame($expected, $final['tags']);
    }

    public function testExtractArrayObject(): void
    {
        $arrayObject = new ArrayObject([
            'value1',
            'value2',
            'value3',
        ]);

        $data = $this->hydrator->extract($arrayObject);

        $this->assertSame(['value1', 'value2', 'value3'], $data);
    }
}
