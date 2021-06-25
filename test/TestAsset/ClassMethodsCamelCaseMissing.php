<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsCamelCaseMissing
{
    /** @var string */
    protected $fooBar = '1';

    /** @var string */
    protected $fooBarBaz = '2';

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

    /*
     * comment to detection verification
     *
    public function setFooBarBaz($value)
    {
        $this->fooBarBaz = $value;
        return $this;
    }
    */
}
