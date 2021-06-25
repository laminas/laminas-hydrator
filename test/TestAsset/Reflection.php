<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class Reflection
{
    /** @var string */
    public $foo = '1';

    /** @var string */
    protected $fooBar = '2';

    /** @var string */
    private $fooBarBaz = '3';

    public function getFooBar(): string
    {
        return $this->fooBar;
    }

    public function getFooBarBaz(): string
    {
        return $this->fooBarBaz;
    }
}
