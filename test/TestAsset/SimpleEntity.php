<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use function array_key_exists;

class SimpleEntity
{
    /** @var mixed */
    public $value;

    /**
     * @param  mixed $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array)
    {
        if (array_key_exists('value', $array)) {
            $this->setValue($array['value']);
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return ['value' => $this->getValue()];
    }
}
