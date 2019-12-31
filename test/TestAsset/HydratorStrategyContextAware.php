<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use Laminas\Hydrator\Strategy\DefaultStrategy;

class HydratorStrategyContextAware extends DefaultStrategy
{
    public $object;
    public $data;

    public function extract($value, ?object $object = null)
    {
        $this->object = $object;
        return $value;
    }

    public function hydrate($value, ?array $data = null)
    {
        $this->data = $data;
        return $value;
    }
}
