<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class HydratorStrategyEntityB
{
    /** @var mixed */
    private $field1;

    /** @var mixed */
    private $field2;

    /**
     * @param mixed $field1
     * @param mixed $field2
     */
    public function __construct($field1, $field2)
    {
        $this->field1 = $field1;
        $this->field2 = $field2;
    }

    /** @return mixed */
    public function getField1()
    {
        return $this->field1;
    }

    /** @return mixed */
    public function getField2()
    {
        return $this->field2;
    }

    /** @param mixed $value */
    public function setField1($value): self
    {
        $this->field1 = $value;
        return $this;
    }

    /** @param mixed $value */
    public function setField2($value): self
    {
        $this->field2 = $value;
        return $this;
    }
}
