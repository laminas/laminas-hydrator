<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

/**
 * @final
 */
class DefaultStrategy implements StrategyInterface
{
    /**
     * @inheritDoc
     */
    public function extract(mixed $value, ?object $object = null): mixed
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate(mixed $value, ?array $data = null): mixed
    {
        return $value;
    }
}
