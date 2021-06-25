<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsCamelCase
{
    /** @var string */
    protected $fooBar = '1';

    /** @var string */
    protected $fooBarBaz = '2';

    /** @var bool */
    protected $isFoo = true;

    /** @var bool */
    protected $isBar = true;

    /** @var bool */
    protected $hasFoo = true;

    /** @var bool */
    protected $hasBar = true;

    /** @return string */
    public function getFooBar()
    {
        return $this->fooBar;
    }

    /** @param string $value */
    public function setFooBar($value): self
    {
        $this->fooBar = $value;
        return $this;
    }

    /** @return string */
    public function getFooBarBaz()
    {
        return $this->fooBarBaz;
    }

    /** @param string $value */
    public function setFooBarBaz($value): self
    {
        $this->fooBarBaz = $value;
        return $this;
    }

    /** @return bool */
    public function getIsFoo()
    {
        return $this->isFoo;
    }

    /** @param bool $value */
    public function setIsFoo($value): self
    {
        $this->isFoo = $value;
        return $this;
    }

    /** @return bool */
    public function isBar()
    {
        return $this->isBar;
    }

    /** @param bool $value */
    public function setIsBar($value): self
    {
        $this->isBar = $value;
        return $this;
    }

    /** @return bool */
    public function getHasFoo()
    {
        return $this->hasFoo;
    }

    /** @param bool $value */
    public function setHasFoo($value): self
    {
        $this->hasFoo = $value;
        return $this;
    }

    /** @return bool */
    public function hasBar()
    {
        return $this->hasBar;
    }

    /** @param bool $value */
    public function setHasBar($value): self
    {
        $this->hasBar = $value;
        return $this;
    }
}
