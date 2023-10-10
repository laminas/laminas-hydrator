<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ReflectionHydrator;
use LaminasTest\Hydrator\TestAsset\ReflectionHydratorTestData;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;
use TypeError;

use function get_parent_class;

#[CoversClass(ReflectionHydrator::class)]
class ReflectionHydratorTest extends TestCase
{
    use HydratorTestTrait;

    /** @var ReflectionHydrator */
    protected $hydrator;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
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

        $this->hydrator->hydrate(['foo' => 'bar'], $argument);
    }

    public function testCanExtractFromAnonymousClass(): void
    {
        $instance = new ReflectionHydratorTestData();
        $this->assertSame([
            'foo' => 'bar',
            'bar' => 'baz',
        ], $this->hydrator->extract($instance));
    }

    public function testCanHydrateAnonymousObject(): void
    {
        $instance = new class {
            private ?string $foo = null;
        };

        $hydrated = $this->hydrator->hydrate(['foo' => 'bar'], $instance);

        $this->assertSame($instance, $hydrated);
        $r = new ReflectionProperty($hydrated, 'foo');
        $this->assertSame('bar', $r->getValue($hydrated));
    }

    public function testCanExtractFromExtendedClass(): void
    {
        $instance = new class extends ReflectionHydratorTestData {
        };
        $this->assertSame([
            'foo' => 'bar',
            'bar' => 'baz',
        ], $this->hydrator->extract($instance, true));
    }

    public function testFailToExtractFromExtendedClass(): void
    {
        $instance = new class extends ReflectionHydratorTestData {
        };
        $this->assertNotSame([
            'foo' => 'bar',
            'bar' => 'baz',
        ], $this->hydrator->extract($instance, false));
    }

    public function testCanHydrateExtendedClass(): void
    {
        $instance = new class extends ReflectionHydratorTestData {
        };

        $hydrated = $this->hydrator->hydrate(['foo' => 'foo-foo'], $instance, true);

        $this->assertSame($instance, $hydrated);
        $r = new ReflectionProperty(get_parent_class($hydrated), 'foo');
        $this->assertSame('foo-foo', $r->getValue($hydrated));
    }

    public function testFailToHydrateExtendedClass(): void
    {
        $instance = new class extends ReflectionHydratorTestData {
        };

        $hydrated = $this->hydrator->hydrate(['foo' => 'foo-foo'], $instance, false);

        $this->assertSame($instance, $hydrated);
        $r = new ReflectionProperty(get_parent_class($hydrated), 'foo');
        $this->assertSame('bar', $r->getValue($hydrated));
    }
}
