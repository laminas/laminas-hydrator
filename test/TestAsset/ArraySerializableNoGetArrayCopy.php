<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\TestAsset;

class ArraySerializableNoGetArrayCopy
{
    protected $data = [];

    public function __construct()
    {
        $this->data = [
            "foo" => "bar",
            "bar" => "foo",
            "blubb" => "baz",
            "quo" => "blubb"
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
     * Returns the internal data
     */
    public function getData()
    {
        return $this->data;
    }
}
