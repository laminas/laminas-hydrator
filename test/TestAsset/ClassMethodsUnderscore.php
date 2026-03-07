<?php // phpcs:disable WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCapsProperty


declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsUnderscore
{
    protected string $foo_bar = '1';

    protected string $foo_bar_baz = '2';

    protected bool $is_foo = true;

    protected bool $is_bar = true;

    protected bool $has_foo = true;

    protected bool $has_bar = true;

    public function getFooBar(): string
    {
        return $this->foo_bar;
    }

    public function setFooBar(string $value): self
    {
        $this->foo_bar = $value;
        return $this;
    }

    public function getFooBarBaz(): string
    {
        return $this->foo_bar_baz;
    }

    public function setFooBarBaz(string $value): self
    {
        $this->foo_bar_baz = $value;
        return $this;
    }

    public function getIsFoo(): bool
    {
        return $this->is_foo;
    }

    public function setIsFoo(bool $value): self
    {
        $this->is_foo = $value;
        return $this;
    }

    public function isBar(): bool
    {
        return $this->is_bar;
    }

    public function setIsBar(bool $value): self
    {
        $this->is_bar = $value;
        return $this;
    }

    public function getHasFoo(): bool
    {
        return $this->has_foo;
    }

    public function setHasFoo(bool $value): self
    {
        $this->has_foo = $value;
        return $this;
    }

    public function hasBar(): bool
    {
        return $this->has_bar;
    }

    public function setHasBar(bool $value): self
    {
        $this->has_bar = $value;
        return $this;
    }
}
