<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ObjectPropertyHydrator;
use LaminasTest\Hydrator\TestAsset\ClassWithPublicStaticProperties;
use LaminasTest\Hydrator\TestAsset\ObjectProperty as ObjectPropertyTestAsset;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * Unit tests for {@see ObjectPropertyHydrator}
 *
 * @covers \Laminas\Hydrator\ObjectPropertyHydrator
 */
class ObjectPropertyHydratorTest extends TestCase
{
    use HydratorTestTrait;

    /**
     * @var ObjectPropertyHydrator
     */
    private $hydrator;

    /**
     * {@inheritDoc}
     */
    protected function setUp() : void
    {
        $this->hydrator = new ObjectPropertyHydrator();
    }

    /**
     * Verify that we get an exception when trying to extract on a non-object
     */
    public function testHydratorExtractThrowsExceptionOnNonObjectParameter()
    {
        $this->expectException(TypeError::class);
        $this->hydrator->extract('thisIsNotAnObject');
    }

    /**
     * Verify that we get an exception when trying to hydrate a non-object
     */
    public function testHydratorHydrateThrowsExceptionOnNonObjectParameter()
    {
        $this->expectException(TypeError::class);
        $this->hydrator->hydrate(['some' => 'data'], 'thisIsNotAnObject');
    }

    /**
     * Verifies that the hydrator can extract from property of stdClass objects
     */
    public function testCanExtractFromStdClass()
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        $this->assertSame(['foo' => 'bar'], $this->hydrator->extract($object));
    }

    /**
     * Verifies that the extraction process works on classes that aren't stdClass
     */
    public function testCanExtractFromGenericClass()
    {
        $this->assertSame(
            [
                'foo' => 'bar',
                'bar' => 'foo',
                'blubb' => 'baz',
                'quo' => 'blubb'
            ],
            $this->hydrator->extract(new ObjectPropertyTestAsset())
        );
    }

    /**
     * Verify hydration of {@see \stdClass}
     */
    public function testCanHydrateStdClass()
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        $object = $this->hydrator->hydrate(['foo' => 'baz'], $object);

        $this->assertEquals('baz', $object->foo);
    }

    /**
     * Verify that new properties are created if the object is stdClass
     */
    public function testCanHydrateAdditionalPropertiesToStdClass()
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        $object = $this->hydrator->hydrate(['foo' => 'baz', 'bar' => 'baz'], $object);

        $this->assertEquals('baz', $object->foo);
        $this->assertObjectHasAttribute('bar', $object);
        $this->assertSame('baz', $object->bar);
    }

    /**
     * Verify that it can hydrate our class public properties
     */
    public function testCanHydrateGenericClassPublicProperties()
    {
        $object = $this->hydrator->hydrate(
            [
                'foo' => 'foo',
                'bar' => 'bar',
                'blubb' => 'blubb',
                'quo' => 'quo',
                'quin' => 'quin'
            ],
            new ObjectPropertyTestAsset()
        );

        $this->assertSame('foo', $object->get('foo'));
        $this->assertSame('bar', $object->get('bar'));
        $this->assertSame('blubb', $object->get('blubb'));
        $this->assertSame('quo', $object->get('quo'));
        $this->assertNotSame('quin', $object->get('quin'));
    }

    /**
     * Verify that it can hydrate new properties on generic classes
     */
    public function testCanHydrateGenericClassNonExistingProperties()
    {
        $object = $this->hydrator->hydrate(['newProperty' => 'newPropertyValue'], new ObjectPropertyTestAsset());

        $this->assertSame('newPropertyValue', $object->get('newProperty'));
    }

    /**
     * Verify that hydration is skipped for class properties (it is an object hydrator after all)
     */
    public function testSkipsPublicStaticClassPropertiesHydration()
    {
        $this->hydrator->hydrate(
            ['foo' => '1', 'bar' => '2', 'baz' => '3'],
            new ClassWithPublicStaticProperties()
        );

        $this->assertSame('foo', ClassWithPublicStaticProperties::$foo);
        $this->assertSame('bar', ClassWithPublicStaticProperties::$bar);
        $this->assertSame('baz', ClassWithPublicStaticProperties::$baz);
    }

    /**
     * Verify that extraction is skipped for class properties (it is an object hydrator after all)
     */
    public function testSkipsPublicStaticClassPropertiesExtraction()
    {
        $this->assertEmpty($this->hydrator->extract(new ClassWithPublicStaticProperties()));
    }
}
