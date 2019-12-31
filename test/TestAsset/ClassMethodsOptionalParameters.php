<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\TestAsset;

/**
 * Test asset to check how optional parameters of are treated methods
 */
class ClassMethodsOptionalParameters
{
    /**
     * @var string
     */
    public $foo = 'bar';

    /**
     * @param mixed $optional
     *
     * @return string
     */
    public function getFoo($optional = null)
    {
        return $this->foo;
    }

    /**
     * @param string $foo
     * @param mixed  $optional
     */
    public function setFoo($foo, $optional = null)
    {
        $this->foo = (string) $foo;
    }
}
