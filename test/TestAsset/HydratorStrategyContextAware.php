<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\TestAsset;

use Laminas\Hydrator\Strategy\DefaultStrategy;

class HydratorStrategyContextAware extends DefaultStrategy
{
    public $object;
    public $data;

    public function extract($value, $object = null)
    {
        $this->object = $object;
        return $value;
    }

    public function hydrate($value, $data = null)
    {
        $this->data = $data;
        return $value;
    }
}
