<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class HydratorClosureStrategyEntity
{
    /** @var mixed */
    public $field1;

    /** @var mixed */
    public $field2;

    /**
     * @param mixed $field1
     * @param mixed $field2
     */
    public function __construct($field1 = null, $field2 = null)
    {
        $this->field1 = $field1;
        $this->field2 = $field2;
    }
}
