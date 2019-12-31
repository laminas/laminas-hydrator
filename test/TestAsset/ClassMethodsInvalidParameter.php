<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsInvalidParameter
{
    public function hasAlias($alias)
    {
        return $alias;
    }

    public function getTest($foo)
    {
        return $foo;
    }

    public function isTest($bar)
    {
        return $bar;
    }

    public function hasBar()
    {
        return true;
    }

    public function getFoo()
    {
        return "Bar";
    }

    public function isBla()
    {
        return false;
    }
}
