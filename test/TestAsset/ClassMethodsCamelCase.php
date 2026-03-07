<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsCamelCase
{
    protected string $fooBar = '1';

    protected string $fooBarBaz = '2';

    protected bool $isFoo = true;

    protected bool $isBar = true;

    protected bool $hasFoo = true;

    protected bool $hasBar = true;

    public function getFooBar(): string
    {
        return $this->fooBar;
    }

    public function setFooBar(string $value): self
    {
        $this->fooBar = $value;
        return $this;
    }

    public function getFooBarBaz(): string
    {
        return $this->fooBarBaz;
    }

    public function setFooBarBaz(string $value): self
    {
        $this->fooBarBaz = $value;
        return $this;
    }

    public function getIsFoo(): bool
    {
        return $this->isFoo;
    }

    public function setIsFoo(bool $value): self
    {
        $this->isFoo = $value;
        return $this;
    }

    public function isBar(): bool
    {
        return $this->isBar;
    }

    public function setIsBar(bool $value): self
    {
        $this->isBar = $value;
        return $this;
    }

    public function getHasFoo(): bool
    {
        return $this->hasFoo;
    }

    public function setHasFoo(bool $value): self
    {
        $this->hasFoo = $value;
        return $this;
    }

    public function hasBar(): bool
    {
        return $this->hasBar;
    }

    public function setHasBar(bool $value): self
    {
        $this->hasBar = $value;
        return $this;
    }
}
