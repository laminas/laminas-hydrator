<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ClassMethods;
use Laminas\Hydrator\Exception\BadMethodCallException;
use Laminas\Hydrator\Exception\InvalidArgumentException;
use LaminasTest\Hydrator\TestAsset\ArraySerializable;
use LaminasTest\Hydrator\TestAsset\ClassMethodsCamelCase;
use LaminasTest\Hydrator\TestAsset\ClassMethodsCamelCaseMissing;
use LaminasTest\Hydrator\TestAsset\ClassMethodsOptionalParameters;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see ClassMethods}
 *
 * @covers \Laminas\Hydrator\ClassMethods
 */
class ClassMethodsTest extends TestCase
{
    use HydratorTestTrait;

    /**
     * @var ClassMethods
     */
    protected $hydrator;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->hydrator = new ClassMethods();
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
    public function testSetOptionsThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The options parameter must be an array or a Traversable'
        );
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
     * Verifies a BadMethodCallException is thrown for extracting a non-object
     */
    public function testExtractNonObjectThrowsBadMethodCallException()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Laminas\Hydrator\ClassMethods::extract expects the provided $object to be a PHP object)'
        );
        $this->hydrator->extract('non-object');
    }

    /**
     * Verifies a BadMethodCallException is thrown for hydrating a non-object
     */
    public function testHydrateNonObjectThrowsBadMethodCallException()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Laminas\Hydrator\ClassMethods::hydrate expects the provided $object to be a PHP object)'
        );
        $this->hydrator->hydrate([], 'non-object');
    }
}
