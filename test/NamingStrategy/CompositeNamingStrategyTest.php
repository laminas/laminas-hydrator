<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy;

use Laminas\Hydrator\NamingStrategy\CompositeNamingStrategy;
use Laminas\Hydrator\NamingStrategy\NamingStrategyInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see CompositeNamingStrategy}
 *
 * @covers \Laminas\Hydrator\NamingStrategy\CompositeNamingStrategy
 */
class CompositeNamingStrategyTest extends TestCase
{
    public function testGetSameNameWhenNoNamingStrategyExistsForTheName(): void
    {
        $compositeNamingStrategy = new CompositeNamingStrategy([
            'foo' => $this->createMock(NamingStrategyInterface::class)
        ]);

        $this->assertEquals('bar', $compositeNamingStrategy->hydrate('bar'));
        $this->assertEquals('bar', $compositeNamingStrategy->extract('bar'));
    }

    public function testUseDefaultNamingStrategy(): void
    {
        /* @var $defaultNamingStrategy NamingStrategyInterface|\PHPUnit_Framework_MockObject_MockObject*/
        $defaultNamingStrategy = $this->createMock(NamingStrategyInterface::class);
        $defaultNamingStrategy->expects($this->once())
            ->method('hydrate')
            ->with('foo')
            ->will($this->returnValue('Foo'));
        $defaultNamingStrategy->expects($this->once())
            ->method('extract')
            ->with('Foo')
            ->will($this->returnValue('foo'));

        $compositeNamingStrategy = new CompositeNamingStrategy(
            ['bar' => $this->createMock(NamingStrategyInterface::class)],
            $defaultNamingStrategy
        );
        $this->assertEquals('Foo', $compositeNamingStrategy->hydrate('foo'));
        $this->assertEquals('foo', $compositeNamingStrategy->extract('Foo'));
    }

    public function testHydrate(): void
    {
        $fooNamingStrategy = $this->createMock(NamingStrategyInterface::class);
        $fooNamingStrategy->expects($this->once())
            ->method('hydrate')
            ->with('foo')
            ->will($this->returnValue('FOO'));
        $compositeNamingStrategy = new CompositeNamingStrategy(['foo' => $fooNamingStrategy]);
        $this->assertEquals('FOO', $compositeNamingStrategy->hydrate('foo'));
    }

    public function testExtract(): void
    {
        $fooNamingStrategy = $this->createMock(NamingStrategyInterface::class);
        $fooNamingStrategy->expects($this->once())
            ->method('extract')
            ->with('FOO')
            ->will($this->returnValue('foo'));
        $compositeNamingStrategy = new CompositeNamingStrategy(['FOO' => $fooNamingStrategy]);
        $this->assertEquals('foo', $compositeNamingStrategy->extract('FOO'));
    }
}
