<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ReflectionHydrator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;
use TypeError;

#[CoversClass(ReflectionHydrator::class)]
final class ReflectionHydratorTest extends TestCase
{
    use HydratorTestTrait;

    protected ReflectionHydrator $hydrator;

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
        $instance = new class {
            private string $foo = 'bar';
            private string $bar = 'baz';
        };
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
}
