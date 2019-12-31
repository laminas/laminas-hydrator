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
    protected function setUp()
    {
        $this->hydrator = new ReflectionHydrator();
    }

    public function testCanExtract()
    {
        $this->assertSame([], $this->hydrator->extract(new stdClass()));
    }

    public function testCanHydrate()
    {
        $object = new stdClass();

        $this->assertSame($object, $this->hydrator->hydrate(['foo' => 'bar'], $object));
    }

    public function testExtractRaisesExceptionForInvalidInput()
    {
        $argument = (int) 1;

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('must be an object');

        $this->hydrator->extract($argument);
    }

    public function testHydrateRaisesExceptionForInvalidArgument()
    {
        $argument = (int) 1;

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('must be an object');

        $this->hydrator->hydrate([ 'foo' => 'bar' ], $argument);
    }
}
