<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use ArrayObject;
use Laminas\Hydrator\ClassMethodsHydrator;
use LaminasTest\Hydrator\TestAsset\ArraySerializable;
use LaminasTest\Hydrator\TestAsset\ClassMethodsCamelCase;
use LaminasTest\Hydrator\TestAsset\ClassMethodsCamelCaseMissing;
use LaminasTest\Hydrator\TestAsset\ClassMethodsOptionalParameters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use TypeError;

#[CoversClass(ClassMethodsHydrator::class)]
final class ClassMethodsHydratorTest extends TestCase
{
    use HydratorTestTrait;

    protected ClassMethodsHydrator $hydrator;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->hydrator = new ClassMethodsHydrator();
    }

    /**
     * Verifies that extraction can happen even when a getter has parameters if those are all optional
     */
    public function testCanExtractFromMethodsWithOptionalParameters(): void
    {
        $this->assertSame(['foo' => 'bar'], $this->hydrator->extract(new ClassMethodsOptionalParameters()));
    }

    /**
     * Verifies that the hydrator can act on different instance types
     */
    public function testCanHydratedPromiscuousInstances(): void
    {
        $classMethodsCamelCase        = $this->hydrator->hydrate(
            ['fooBar' => 'baz-tab'],
            new ClassMethodsCamelCase()
        );
        $classMethodsCamelCaseMissing = $this->hydrator->hydrate(
            ['fooBar' => 'baz-tab'],
            new ClassMethodsCamelCaseMissing()
        );
        $arraySerializable            = $this->hydrator->hydrate(
            ['fooBar' => 'baz-tab'],
            new ArraySerializable()
        );

        $this->assertSame('baz-tab', $classMethodsCamelCase->getFooBar());
        $this->assertSame('baz-tab', $classMethodsCamelCaseMissing->getFooBar());
        $this->assertSame(
            [
                "foo"   => "bar",
                "bar"   => "foo",
                "blubb" => "baz",
                "quo"   => "blubb",
            ],
            $arraySerializable->getArrayCopy()
        );
    }

    /**
     * Verifies the options must be an array or Traversable
     */
    public function testSetOptionsThrowsException(): void
    {
        $this->expectException(TypeError::class);
        $this->hydrator->setOptions('invalid options');
    }

    /**
     * Verifies options can be set from a Traversable object
     */
    public function testSetOptionsFromTraversable(): void
    {
        $options = new ArrayObject([
            'underscoreSeparatedKeys' => false,
        ]);
        $this->hydrator->setOptions($options);

        $this->assertFalse($this->hydrator->getUnderscoreSeparatedKeys());
    }

    /**
     * Verifies a TypeError is thrown for extracting a non-object
     */
    public function testExtractNonObjectThrowsTypeError(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('object');
        $this->hydrator->extract('non-object');
    }

    /**
     * Verifies a TypeError is thrown for hydrating a non-object
     */
    public function testHydrateNonObjectThrowsTypeError(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('object');
        $this->hydrator->hydrate([], 'non-object');
    }

    public function testExtractClassWithoutAnyMethod(): void
    {
        $data = $this->hydrator->extract(
            new TestAsset\ClassWithoutAnyMethod()
        );
        $this->assertSame([], $data);
    }

    public function testCanExtractFromAnonymousClassMethods(): void
    {
        $anonymous = new class extends ClassMethodsOptionalParameters {
        };
        $this->assertSame(['foo' => 'bar'], $this->hydrator->extract($anonymous));
    }
}
