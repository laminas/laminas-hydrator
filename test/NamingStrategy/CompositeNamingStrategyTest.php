<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy;

use Laminas\Hydrator\NamingStrategy\CompositeNamingStrategy;
use Laminas\Hydrator\NamingStrategy\NamingStrategyInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(CompositeNamingStrategy::class)]
final class CompositeNamingStrategyTest extends TestCase
{
    public function testGetSameNameWhenNoNamingStrategyExistsForTheName(): void
    {
        $compositeNamingStrategy = new CompositeNamingStrategy([
            'foo' => $this->createMock(NamingStrategyInterface::class),
        ]);

        $this->assertSame('bar', $compositeNamingStrategy->hydrate('bar'));
        $this->assertSame('bar', $compositeNamingStrategy->extract('bar'));
    }

    public function testUseDefaultNamingStrategy(): void
    {
        /** @var NamingStrategyInterface&MockObject $defaultNamingStrategy */
        $defaultNamingStrategy = $this->createMock(NamingStrategyInterface::class);
        $defaultNamingStrategy->expects($this->once())
            ->method('hydrate')
            ->with('foo')
            ->willReturn('Foo');
        $defaultNamingStrategy->expects($this->once())
            ->method('extract')
            ->with('Foo')
            ->willReturn('foo');

        $compositeNamingStrategy = new CompositeNamingStrategy(
            ['bar' => $this->createMock(NamingStrategyInterface::class)],
            $defaultNamingStrategy
        );
        $this->assertSame('Foo', $compositeNamingStrategy->hydrate('foo'));
        $this->assertSame('foo', $compositeNamingStrategy->extract('Foo'));
    }

    public function testHydrate(): void
    {
        $fooNamingStrategy = $this->createMock(NamingStrategyInterface::class);
        $fooNamingStrategy->expects($this->once())
            ->method('hydrate')
            ->with('foo')
            ->willReturn('FOO');
        $compositeNamingStrategy = new CompositeNamingStrategy(['foo' => $fooNamingStrategy]);
        $this->assertSame('FOO', $compositeNamingStrategy->hydrate('foo'));
    }

    public function testExtract(): void
    {
        $fooNamingStrategy = $this->createMock(NamingStrategyInterface::class);
        $fooNamingStrategy->expects($this->once())
            ->method('extract')
            ->with('FOO')
            ->willReturn('foo');
        $compositeNamingStrategy = new CompositeNamingStrategy(['FOO' => $fooNamingStrategy]);
        $this->assertSame('foo', $compositeNamingStrategy->extract('FOO'));
    }
}
