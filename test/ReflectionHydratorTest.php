<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ReflectionHydrator;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;
use TypeError;

/**
 * Unit tests for {@see ReflectionHydrator}
 *
 * @covers \Laminas\Hydrator\ReflectionHydrator
 */
class ReflectionHydratorTest extends TestCase
{
    use HydratorTestTrait;

    /**
     * @var ReflectionHydrator
     */
    protected $hydrator;

    /**
     * {@inheritDoc}
     */
    protected function setUp() : void
    {
        $this->hydrator = new ReflectionHydrator();
    }

    public function testCanExtract(): void
    {
        $this->assertSame([], $this->hydrator->extract(new stdClass()));
    }

    public function testCanHydrate(): void
    {
        $object = new stdClass();

        $this->assertSame($object, $this->hydrator->hydrate(['foo' => 'bar'], $object));
    }

    public function testExtractRaisesExceptionForInvalidInput(): void
    {
        $argument = 1;

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('object');

        $this->hydrator->extract($argument);
    }

    public function testHydrateRaisesExceptionForInvalidArgument(): void
    {
        $argument = 1;

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('object');

        $this->hydrator->hydrate([ 'foo' => 'bar' ], $argument);
    }

    public function testCanExtractFromAnonymousClass(): void
    {
        $instance = new class {
            /** @var string */
            private $foo = 'bar';
            /** @var string */
            private $bar = 'baz';
        };
        $this->assertSame([
            'foo' => 'bar',
            'bar' => 'baz',
        ], $this->hydrator->extract($instance));
    }

    public function testCanHydrateAnonymousObject(): void
    {
        $instance = new class {
            /** @var null|string */
            private $foo;
        };

        $hydrated = $this->hydrator->hydrate(['foo' => 'bar'], $instance);

        $this->assertSame($instance, $hydrated);
        $r = new ReflectionProperty($hydrated, 'foo');
        $r->setAccessible(true);
        $this->assertSame('bar', $r->getValue($hydrated));
    }
}
