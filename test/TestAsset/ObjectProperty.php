<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class ObjectProperty
{
    public string $foo;

    public string $bar;

    public string $blubb;

    public string $quo;

    protected string $quin;

    public function __construct()
    {
        $this->foo   = 'bar';
        $this->bar   = 'foo';
        $this->blubb = 'baz';
        $this->quo   = 'blubb';
        $this->quin  = 'five';
    }

    public function get(string $name): string
    {
        return $this->$name;
    }
}
