<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Serializer\Adapter\AdapterInterface as SerializerAdapter;

final class SerializableStrategy implements StrategyInterface
{
    public function __construct(private readonly SerializerAdapter $serializer)
    {
    }

    /**
     * Serialize the given value so that it can be extracted by the hydrator.
     *
     * {@inheritDoc}
     */
    public function extract(mixed $value, ?object $object = null): string
    {
        return $this->serializer->serialize($value);
    }

    /**
     * Unserialize the given value so that it can be hydrated by the hydrator.
     *
     * {@inheritDoc}
     */
    public function hydrate(mixed $value, ?array $data = null): mixed
    {
        return $this->serializer->unserialize($value);
    }
}
