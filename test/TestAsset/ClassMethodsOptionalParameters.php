<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

/**
 * Test asset to check how optional parameters of are treated methods
 */
class ClassMethodsOptionalParameters
{
    public string $foo = 'bar';

    public function getFoo(mixed $optional = null): string
    {
        return $this->foo;
    }

    public function setFoo(string $foo, mixed $optional = null): void
    {
        $this->foo = $foo;
    }
}
