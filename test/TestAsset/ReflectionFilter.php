<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\TestAsset;

class ReflectionFilter
{
    protected $foo = null;
    protected $bar = null;
    protected $blubb = null;
    protected $quo = null;

    public function __construct()
    {
        $this->foo = "bar";
        $this->bar = "foo";
        $this->blubb = "baz";
        $this->quo = "blubb";
    }
}
