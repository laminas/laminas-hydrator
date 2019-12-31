<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsCamelCaseMissing
{
    protected $fooBar = '1';

    protected $fooBarBaz = '2';

    public function getFooBar()
    {
        return $this->fooBar;
    }

    public function setFooBar($value)
    {
        $this->fooBar = $value;
        return $this;
    }

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
