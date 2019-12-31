<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ClassMethodsHydrator;
use LaminasTest\Hydrator\TestAsset\ArraySerializable;
use LaminasTest\Hydrator\TestAsset\ClassMethodsCamelCase;
use LaminasTest\Hydrator\TestAsset\ClassMethodsCamelCaseMissing;
use LaminasTest\Hydrator\TestAsset\ClassMethodsOptionalParameters;
use PHPUnit\Framework\TestCase;
use TypeError;

/**
 * Unit tests for {@see ClassMethodsHydrator}
 *
 * @covers \Laminas\Hydrator\ClassMethodsHydrator
 */
class ClassMethodsHydratorTest extends TestCase
{
    use HydratorTestTrait;

    /**
     * @var ClassMethodsHydrator
     */
    protected $hydrator;

    /**
     * {@inheritDoc}
     */
    protected function setUp() : void
    {
        $this->hydrator = new ClassMethodsHydrator();
    }

    /**
     * Verifies that extraction can happen even when a getter has parameters if those are all optional
     */
    public function testCanExtractFromMethodsWithOptionalParameters()
    {
        $this->assertSame(['foo' => 'bar'], $this->hydrator->extract(new ClassMethodsOptionalParameters()));
    }

    /**
     * Verifies that the hydrator can act on different instance types
     */
    public function testCanHydratedPromiscuousInstances()
    {
        /* @var $classMethodsCamelCase ClassMethodsCamelCase */
        $classMethodsCamelCase = $this->hydrator->hydrate(
            ['fooBar' => 'baz-tab'],
            new ClassMethodsCamelCase()
        );
        /* @var $classMethodsCamelCaseMissing ClassMethodsCamelCaseMissing */
        $classMethodsCamelCaseMissing = $this->hydrator->hydrate(
            ['fooBar' => 'baz-tab'],
            new ClassMethodsCamelCaseMissing()
        );
        /* @var $arraySerializable ArraySerializable */
        $arraySerializable = $this->hydrator->hydrate(['fooBar' => 'baz-tab'], new ArraySerializable());

        $this->assertSame('baz-tab', $classMethodsCamelCase->getFooBar());
        $this->assertSame('baz-tab', $classMethodsCamelCaseMissing->getFooBar());
        $this->assertSame(
            [
                "foo" => "bar",
                "bar" => "foo",
                "blubb" => "baz",
                "quo" => "blubb"
            ],
            $arraySerializable->getArrayCopy()
        );
    }

    /**
     * Verifies the options must be an array or Traversable
     */
    public function testSetOptionsThrowsException()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('must be iterable');
        $this->hydrator->setOptions('invalid options');
    }

    /**
     * Verifies options can be set from a Traversable object
     */
    public function testSetOptionsFromTraversable()
    {
        $options = new \ArrayObject([
            'underscoreSeparatedKeys' => false,
        ]);
        $this->hydrator->setOptions($options);

        $this->assertSame(false, $this->hydrator->getUnderscoreSeparatedKeys());
    }

    /**
     * Verifies a TypeError is thrown for extracting a non-object
     */
    public function testExtractNonObjectThrowsTypeError()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('must be an object');
        $this->hydrator->extract('non-object');
    }

    /**
     * Verifies a TypeError is thrown for hydrating a non-object
     */
    public function testHydrateNonObjectThrowsTypeError()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('must be an object');
        $this->hydrator->hydrate([], 'non-object');
    }
}
