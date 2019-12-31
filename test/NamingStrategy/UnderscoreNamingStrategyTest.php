<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy;

use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see UnderscoreNamingStrategy}
 *
 * @covers \Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy
 */
class UnderscoreNamingStrategyTest extends TestCase
{
    public function testNameHydratesToCamelCase()
    {
        $strategy = new UnderscoreNamingStrategy();
        $this->assertEquals('fooBarBaz', $strategy->hydrate('foo_bar_baz'));
    }

    public function testNameExtractsToUnderscore()
    {
        $strategy = new UnderscoreNamingStrategy();
        $this->assertEquals('foo_bar_baz', $strategy->extract('fooBarBaz'));
    }

    /**
     * @group 6422
     * @group 6420
     */
    public function testNameHydratesToStudlyCaps()
    {
        $strategy = new UnderscoreNamingStrategy();

        $this->assertEquals('fooBarBaz', $strategy->hydrate('Foo_Bar_Baz'));
    }
}
