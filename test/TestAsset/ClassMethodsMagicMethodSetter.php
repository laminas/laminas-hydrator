<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsMagicMethodSetter
{
    protected $foo;

    public function __call($method, $args)
    {
        if (strlen($method) > 3 && strtolower(substr($method, 3)) == 'foo') {
            $this->foo = $args[0];
        }
    }

    public function getFoo()
    {
        return $this->foo;
    }
}
