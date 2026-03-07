<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use function array_key_exists;

class SimpleEntity
{
    public mixed $value;

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function getValue(): mixed
    {
        return $this->value ?? null;
    }

    /**
     * Exchange internal values from provided array
     */
    public function exchangeArray(array $array): void
    {
        if (array_key_exists('value', $array)) {
            $this->setValue($array['value']);
        }
    }

    /**
     * Return an array representation of the object
     */
    public function getArrayCopy(): array
    {
        return ['value' => $this->getValue()];
    }
}
