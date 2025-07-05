<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsInvalidParameter
{
    /**
     * @param mixed $alias
     * @return mixed
     */
    public function hasAlias($alias)
    {
        return $alias;
    }

    /**
     * @param mixed $foo
     * @return mixed
     */
    public function getTest($foo)
    {
        return $foo;
    }

    /** @param mixed $bar */
    public function isTest($bar): bool
    {
        return $bar;
    }

    public function hasBar(): bool
    {
        return true;
    }

    public function getFoo(): string
    {
        return "Bar";
    }

    public function isBla(): bool
    {
        return false;
    }
}
