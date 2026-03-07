<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use Laminas\Stdlib\ArraySerializableInterface;

class ArraySerializable implements ArraySerializableInterface
{
    protected array $data = [];

    public function __construct()
    {
        $this->data = [
            "foo"   => "bar",
            "bar"   => "foo",
            "blubb" => "baz",
            "quo"   => "blubb",
        ];
    }

    /**
     * Exchange internal values from provided array
     */
    public function exchangeArray(array $array): void
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
