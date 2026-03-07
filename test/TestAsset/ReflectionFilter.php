<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ReflectionFilter
{
    protected string $foo;

    protected string $bar;

    protected string $blubb;

    protected string $quo;

    public function __construct()
    {
        $this->foo   = 'bar';
        $this->bar   = 'foo';
        $this->blubb = 'baz';
        $this->quo   = 'blubb';
    }
}
