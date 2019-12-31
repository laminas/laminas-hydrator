<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class Reflection
{
    public $foo = '1';

    protected $fooBar = '2';

    private $fooBarBaz = '3';

    public function getFooBar()
    {
        return $this->fooBar;
    }

    public function getFooBarBaz()
    {
        return $this->fooBarBaz;
    }
}
