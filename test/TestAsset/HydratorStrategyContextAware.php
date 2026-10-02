<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use Laminas\Hydrator\Strategy\DefaultStrategy;

final class HydratorStrategyContextAware extends DefaultStrategy
{
    public object $object;

    public mixed $data;

    public function extract(mixed $value, ?object $object = null): mixed
    {
        $this->object = $object;
        return $value;
    }

    public function hydrate(mixed $value, ?array $data = null): mixed
    {
        $this->data = $data;
        return $value;
    }
}
