<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ReflectionFilter
{
    /** @var string */
    protected $foo;

    /** @var string */
    protected $bar;

    /** @var string */
    protected $blubb;

    /** @var string */
    protected $quo;

    public function __construct()
    {
        $this->foo   = 'bar';
        $this->bar   = 'foo';
        $this->blubb = 'baz';
        $this->quo   = 'blubb';
    }
}
