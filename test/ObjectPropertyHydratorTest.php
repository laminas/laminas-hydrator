<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ObjectPropertyHydrator;
use LaminasTest\Hydrator\TestAsset\ClassWithPublicStaticProperties;
use LaminasTest\Hydrator\TestAsset\ObjectProperty as ObjectPropertyTestAsset;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

#[CoversClass(ObjectPropertyHydrator::class)]
final class ObjectPropertyHydratorTest extends TestCase
{
    use HydratorTestTrait;

    private ObjectPropertyHydrator $hydrator;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->hydrator = new ObjectPropertyHydrator();
    }

    /**
     * Verify that we get an exception when trying to extract on a non-object
     */
    public function testHydratorExtractThrowsExceptionOnNonObjectParameter(): void
    {
        $this->expectException(TypeError::class);
        $this->hydrator->extract('thisIsNotAnObject');
    }

    /**
     * Verify that we get an exception when trying to hydrate a non-object
     */
    public function testHydratorHydrateThrowsExceptionOnNonObjectParameter(): void
    {
        $this->expectException(TypeError::class);
        $this->hydrator->hydrate(['some' => 'data'], 'thisIsNotAnObject');
    }

    /**
     * Verifies that the hydrator can extract from property of stdClass objects
     */
    public function testCanExtractFromStdClass(): void
    {
        $object      = new stdClass();
        $object->foo = 'bar';

        $this->assertSame(['foo' => 'bar'], $this->hydrator->extract($object));
    }

    /**
     * Verifies that the extraction process works on classes that aren't stdClass
     */
    public function testCanExtractFromGenericClass(): void
    {
        $this->assertSame(
            [
                'foo'   => 'bar',
                'bar'   => 'foo',
                'blubb' => 'baz',
                'quo'   => 'blubb',
            ],
            $this->hydrator->extract(new ObjectPropertyTestAsset())
        );
    }

    /**
     * Verify hydration of {@see \stdClass}
     */
    public function testCanHydrateStdClass(): void
    {
        $object      = new stdClass();
        $object->foo = 'bar';

        $object = $this->hydrator->hydrate(['foo' => 'baz'], $object);

        $this->assertEquals('baz', $object->foo);
    }

    /**
     * Verify that new properties are created if the object is stdClass
     */
    public function testCanHydrateAdditionalPropertiesToStdClass(): void
    {
        $object      = new stdClass();
        $object->foo = 'bar';

        $object = $this->hydrator->hydrate(['foo' => 'baz', 'bar' => 'baz'], $object);

        $this->assertEquals('baz', $object->foo);
        $this->assertObjectHasProperty('bar', $object);
        $this->assertSame('baz', $object->bar);
    }

    /**
     * Verify that it can hydrate our class public properties
     */
    public function testCanHydrateGenericClassPublicProperties(): void
    {
        $object = $this->hydrator->hydrate(
            [
                'foo'   => 'foo',
                'bar'   => 'bar',
                'blubb' => 'blubb',
                'quo'   => 'quo',
                'quin'  => 'quin',
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
    public function testCanHydrateGenericClassNonExistingProperties(): void
    {
        $object = $this->hydrator->hydrate(['newProperty' => 'newPropertyValue'], new ObjectPropertyTestAsset());

        $this->assertSame('newPropertyValue', $object->get('newProperty'));
    }

    /**
     * Verify that hydration is skipped for class properties (it is an object hydrator after all)
     */
    public function testSkipsPublicStaticClassPropertiesHydration(): void
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
    public function testSkipsPublicStaticClassPropertiesExtraction(): void
    {
        $this->assertEmpty($this->hydrator->extract(new ClassWithPublicStaticProperties()));
    }

    public function testCanExtractFromAnonymousClass(): void
    {
        /** @psalm-var ObjectPropertyTestAsset $anonymous */
        $anonymous = new class extends ObjectPropertyTestAsset {
        };
        $this->assertSame(
            [
                'foo'   => 'bar',
                'bar'   => 'foo',
                'blubb' => 'baz',
                'quo'   => 'blubb',
            ],
            $this->hydrator->extract($anonymous)
        );
    }

    public function testCanHydrateAnonymousClass(): void
    {
        /** @psalm-var ObjectPropertyTestAsset $object */
        $object = $this->hydrator->hydrate(
            [
                'foo'   => 'foo',
                'bar'   => 'bar',
                'blubb' => 'blubb',
                'quo'   => 'quo',
                'quin'  => 'quin',
            ],
            new class extends ObjectPropertyTestAsset {
            }
        );

        $this->assertSame('foo', $object->get('foo'));
        $this->assertSame('bar', $object->get('bar'));
        $this->assertSame('blubb', $object->get('blubb'));
        $this->assertSame('quo', $object->get('quo'));
        $this->assertNotSame('quin', $object->get('quin'));
    }
}
