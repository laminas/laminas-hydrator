<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use ArrayObject;
use Laminas\Hydrator\ArraySerializableHydrator;
use LaminasTest\Hydrator\TestAsset\ArraySerializable as ArraySerializableAsset;
use PHPUnit\Framework\TestCase;
use TypeError;

use function array_merge;

/**
 * Unit tests for {@see ArraySerializableHydrator}
 *
 * @covers \Laminas\Hydrator\ArraySerializableHydrator
 */
class ArraySerializableHydratorTest extends TestCase
{
    use HydratorTestTrait;

    /**
     * @var ArraySerializableHydrator
     */
    protected $hydrator;

    /**
     * {@inheritDoc}
     */
    protected function setUp() : void
    {
        $this->hydrator = new ArraySerializableHydrator();
    }

    /**
     * Verify that we get an exception when trying to extract on a non-object
     */
    public function testHydratorExtractThrowsExceptionOnNonObjectParameter()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('object');
        $this->hydrator->extract('thisIsNotAnObject');
    }

    /**
     * Verify that we get an exception when trying to hydrate a non-object
     */
    public function testHydratorHydrateThrowsExceptionOnNonObjectParameter()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('object');
        $this->hydrator->hydrate(['some' => 'data'], 'thisIsNotAnObject');
    }

    /**
     * Verifies that we can extract from an ArraySerializableInterface
     */
    public function testCanExtractFromArraySerializableObject()
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
    public function testCanHydrateToArraySerializableObject()
    {
        $data = [
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
     *
     * @group 65
     */
    public function testWillPreserveOriginalPropsAtHydration()
    {
        $original = new ArraySerializableAsset();

        $data = [
            'bar' => 'foo1'
        ];

        $expected = array_merge($original->getArrayCopy(), $data);

        $actual = $this->hydrator->hydrate($data, $original);

        $this->assertSame($expected, $actual->getArrayCopy());
    }

    /**
     * To preserve backwards compatibility, if getArrayCopy() is not implemented
     * by the to-be hydrated object, simply exchange the array
     *
     * @group 65
     */
    public function testWillReplaceArrayIfNoGetArrayCopy()
    {
        $original = new \LaminasTest\Hydrator\TestAsset\ArraySerializableNoGetArrayCopy();

        $data = [
                'bar' => 'foo1'
        ];

        $expected = $data;

        $actual = $this->hydrator->hydrate($data, $original);
        $this->assertSame($expected, $actual->getData());
    }

    public function arrayDataProvider()
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
     * @group 66
     * @dataProvider arrayDataProvider
     */
    public function testHydrationWillReplaceNestedArrayData($start, $submit, $expected)
    {
        $original = new ArraySerializableAsset();
        $original->exchangeArray([
            'tags' => $start,
        ]);

        $data = ['tags' => $submit];

        $actual = $this->hydrator->hydrate($data, $original);

        $final = $original->getArrayCopy();

        $this->assertSame($expected, $final['tags']);
    }

    public function testExtractArrayObject()
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
