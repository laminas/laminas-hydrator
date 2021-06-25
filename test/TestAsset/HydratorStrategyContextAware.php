<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use Laminas\Hydrator\Strategy\DefaultStrategy;

class HydratorStrategyContextAware extends DefaultStrategy
{
    /** @var object */
    public $object;

    /** @var mixed */
    public $data;

    /**
     * @param array $value
     * @return mixed
     */
    public function extract($value, ?object $object = null)
    {
        $this->object = $object;
        return $value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function hydrate($value, ?array $data = null)
    {
        $this->data = $data;
        return $value;
    }
}
