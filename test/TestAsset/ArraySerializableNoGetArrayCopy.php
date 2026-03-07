<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ArraySerializableNoGetArrayCopy
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
     * Returns the internal data
     */
    public function getData(): array
    {
        return $this->data;
    }
}
