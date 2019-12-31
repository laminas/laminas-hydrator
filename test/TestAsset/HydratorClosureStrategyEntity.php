<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class HydratorClosureStrategyEntity
{
    public $field1;
    public $field2;

    public function __construct($field1 = null, $field2 = null)
    {
        $this->field1 = $field1;
        $this->field2 = $field2;
    }
}
