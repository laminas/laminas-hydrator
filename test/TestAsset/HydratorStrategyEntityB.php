<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class HydratorStrategyEntityB
{
    public function __construct(private mixed $field1, private mixed $field2)
    {
    }

    public function getField1(): mixed
    {
        return $this->field1;
    }

    public function getField2(): mixed
    {
        return $this->field2;
    }

    public function setField1(mixed $value): self
    {
        $this->field1 = $value;
        return $this;
    }

    public function setField2(mixed $value): self
    {
        $this->field2 = $value;
        return $this;
    }
}
