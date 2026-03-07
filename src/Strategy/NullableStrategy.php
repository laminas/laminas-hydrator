<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

final class NullableStrategy implements StrategyInterface
{
    public function __construct(
        private readonly StrategyInterface $strategy,
        private readonly bool $treatEmptyAsNull = false
    ) {
    }

    /**
     * Check the given value for NULL or empty string so that it can be extracted by the hydrator.
     *
     * {@inheritDoc}
     */
    public function extract(mixed $value, ?object $object = null): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($this->treatEmptyAsNull && $value === '') {
            return null;
        }

        return $this->strategy->extract($value, $object);
    }

    /**
     * Check the given value for NULL or empty string so that it can be hydrated by the hydrator.
     *
     * {@inheritDoc}
     */
    public function hydrate(mixed $value, ?array $data = null): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($this->treatEmptyAsNull && $value === '') {
            return null;
        }

        return $this->strategy->hydrate($value, $data);
    }
}
