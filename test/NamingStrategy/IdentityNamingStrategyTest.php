<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy;

use Laminas\Hydrator\NamingStrategy\IdentityNamingStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see IdentityNamingStrategy}
 *
 * @covers \Laminas\Hydrator\NamingStrategy\IdentityNamingStrategy
 */
class IdentityNamingStrategyTest extends TestCase
{
    /**
     * @dataProvider getTestedNames
     * @param string $name
     */
    public function testHydrate($name): void
    {
        $namingStrategy = new IdentityNamingStrategy();

        $this->assertSame($name, $namingStrategy->hydrate($name));
    }

    /**
     * @dataProvider getTestedNames
     * @param string $name
     */
    public function testExtract($name): void
    {
        $namingStrategy = new IdentityNamingStrategy();

        $this->assertSame($name, $namingStrategy->extract($name));
    }

    /**
     * Data provider
     *
     * @return string[][]
     */
    public function getTestedNames()
    {
        return [
            'foo' => ['foo'],
            'bar' => ['bar'],
        ];
    }
}
