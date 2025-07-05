<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Serializer\Adapter\AdapterInterface as SerializerAdapter;

class SerializableStrategy implements StrategyInterface
{
    protected SerializerAdapter $serializer;

    public function __construct(SerializerAdapter $serializer)
    {
        $this->setSerializer($serializer);
    }

    /**
     * Serialize the given value so that it can be extracted by the hydrator.
     *
     * {@inheritDoc}
     */
    public function extract($value, ?object $object = null)
    {
        $serializer = $this->getSerializer();
        return $serializer->serialize($value);
    }

    /**
     * Unserialize the given value so that it can be hydrated by the hydrator.
     *
     * {@inheritDoc}
     */
    public function hydrate($value, ?array $data = null)
    {
        $serializer = $this->getSerializer();
        return $serializer->unserialize($value);
    }

    /**
     * Set serializer
     *
     * @throws InvalidArgumentException For invalid $serializer values.
     */
    public function setSerializer(SerializerAdapter $serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * Get serializer
     */
    public function getSerializer(): SerializerAdapter
    {
        return $this->serializer;
    }
}
