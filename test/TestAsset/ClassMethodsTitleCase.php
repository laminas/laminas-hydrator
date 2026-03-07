<?php // phpcs:disable

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsTitleCase
{
    protected string $FooBar = '1';

    protected string $FooBarBaz = '2';

    protected bool $IsFoo = true;

    protected bool $IsBar = true;

    protected bool $HasFoo = true;

    protected bool $HasBar = true;

    public function getFooBar(): string
    {
        return $this->FooBar;
    }

    public function setFooBar(string $value): self
    {
        $this->FooBar = $value;
        return $this;
    }

    public function getFooBarBaz(): string
    {
        return $this->FooBarBaz;
    }

    public function setFooBarBaz(string $value): self
    {
        $this->FooBarBaz = $value;
        return $this;
    }

    public function getIsFoo(): bool
    {
        return $this->IsFoo;
    }

    public function setIsFoo(bool $IsFoo): self
    {
        $this->IsFoo = $IsFoo;
        return $this;
    }

    public function getIsBar(): bool
    {
        return $this->IsBar;
    }

    public function setIsBar(bool $IsBar): self
    {
        $this->IsBar = $IsBar;
        return $this;
    }

    public function getHasFoo(): bool
    {
        return $this->HasFoo;
    }

    public function getHasBar(): bool
    {
        return $this->HasBar;
    }

    public function setHasFoo(bool $HasFoo): self
    {
        $this->HasFoo = $HasFoo;
        return $this;
    }

    public function setHasBar(bool $HasBar): void
    {
        $this->HasBar = $HasBar;
    }
}
