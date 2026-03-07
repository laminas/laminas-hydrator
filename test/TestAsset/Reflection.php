<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class Reflection
{
    public string $foo = '1';

    protected string $fooBar = '2';

    private string $fooBarBaz = '3';

    public function getFooBar(): string
    {
        return $this->fooBar;
    }

    public function getFooBarBaz(): string
    {
        return $this->fooBarBaz;
    }
}
