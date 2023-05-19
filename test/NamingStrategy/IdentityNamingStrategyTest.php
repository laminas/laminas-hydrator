<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy;

use Laminas\Hydrator\NamingStrategy\IdentityNamingStrategy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IdentityNamingStrategyTest extends TestCase
{
    /**
     * @param string $name
     */
    #[DataProvider('getTestedNames')]
    public function testHydrate($name): void
    {
        $namingStrategy = new IdentityNamingStrategy();

        $this->assertSame($name, $namingStrategy->hydrate($name));
    }

    /**
     * @param string $name
     */
    #[DataProvider('getTestedNames')]
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
    public static function getTestedNames()
    {
        return [
            'foo' => ['foo'],
            'bar' => ['bar'],
        ];
    }
}
