<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use Laminas\Stdlib\ArraySerializableInterface;

class ArraySerializable implements ArraySerializableInterface
{
    protected $data = [];

    public function __construct()
    {
        $this->data = [
            "foo"   => "bar",
            "bar"   => "foo",
            "blubb" => "baz",
            "quo"   => "blubb"
        ];
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array)
    {
        $this->data = $array;
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return $this->data;
    }
}
