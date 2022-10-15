<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Hydrator\Strategy\StrategyInterface;

class NullableStrategy implements StrategyInterface
{
    public function __construct(private StrategyInterface $strategy, private bool $treatEmptyAsNull = false)
    {
    }

    /**
     * Check the given value for NULL or empty string so that it can be extracted by the hydrator.
     *
     * {@inheritDoc}
     */
    public function extract($value, ?object $object = null)
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
    public function hydrate($value, ?array $data = null)
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
