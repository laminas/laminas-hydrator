<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsUnderscore
{
    protected $foo_bar = '1';

    protected $foo_bar_baz = '2';

    protected $is_foo = true;

    protected $is_bar = true;

    protected $has_foo = true;

    protected $has_bar = true;

    public function getFooBar()
    {
        return $this->foo_bar;
    }

    public function setFooBar($value): self
    {
        $this->foo_bar = $value;
        return $this;
    }

    public function getFooBarBaz()
    {
        return $this->foo_bar_baz;
    }

    public function setFooBarBaz($value): self
    {
        $this->foo_bar_baz = $value;
        return $this;
    }

    public function getIsFoo()
    {
        return $this->is_foo;
    }

    public function setIsFoo($value): self
    {
        $this->is_foo = $value;
        return $this;
    }

    public function isBar()
    {
        return $this->is_bar;
    }

    public function setIsBar($value): self
    {
        $this->is_bar = $value;
        return $this;
    }

    public function getHasFoo()
    {
        return $this->has_foo;
    }

    public function setHasFoo($value): self
    {
        $this->has_foo = $value;
        return $this;
    }

    public function hasBar()
    {
        return $this->has_bar;
    }

    public function setHasBar($value): self
    {
        $this->has_bar = $value;
        return $this;
    }
}
