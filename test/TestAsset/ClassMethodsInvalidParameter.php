<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

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

    /**
     * @return true
     */
    public function hasBar(): bool
    {
        return true;
    }

    public function getFoo(): string
    {
        return "Bar";
    }

    /**
     * @return false
     */
    public function isBla(): bool
    {
        return false;
    }
}
