<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Serializer\Adapter\AdapterInterface as SerializerAdapter;
use Laminas\Serializer\Serializer as SerializerFactory;

use function gettype;
use function is_array;
use function is_object;
use function is_string;
use function iterator_to_array;
use function sprintf;

class SerializableStrategy implements StrategyInterface
{
    /** @var string|SerializerAdapter */
    protected $serializer;

    /** @var mixed[] */
    protected $serializerOptions = [];

    /**
     * @param string|SerializerAdapter $serializer
     * @param null|mixed[] $serializerOptions
     */
    public function __construct($serializer, ?iterable $serializerOptions = null)
    {
        $this->setSerializer($serializer);
        if ($serializerOptions) {
            $this->setSerializerOptions($serializerOptions);
        }
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
     * @param  mixed $serializer Should be a string or
     *     SerializerAdapter instance
     * @throws InvalidArgumentException For invalid $serializer values.
     */
    public function setSerializer(mixed $serializer): void
    {
        if (! is_string($serializer) && ! $serializer instanceof SerializerAdapter) {
            throw new InvalidArgumentException(sprintf(
                '%s expects either a string serializer name or Laminas\Serializer\Adapter\AdapterInterface instance; '
                . 'received "%s"',
                __METHOD__,
                is_object($serializer) ? $serializer::class : gettype($serializer)
            ));
        }
        $this->serializer = $serializer;
    }

    /**
     * Get serializer
     */
    public function getSerializer(): SerializerAdapter
    {
        if (is_string($this->serializer)) {
            $options          = $this->getSerializerOptions();
            $this->serializer = SerializerFactory::factory($this->serializer, $options);
        } elseif (null === $this->serializer) {
            $this->serializer = SerializerFactory::getDefaultAdapter();
        }

        return $this->serializer;
    }

    /**
     * Set configuration options for instantiating a serializer adapter
     *
     * @param mixed[] $serializerOptions
     */
    public function setSerializerOptions(iterable $serializerOptions): void
    {
        $this->serializerOptions = is_array($serializerOptions)
            ? $serializerOptions
            : iterator_to_array($serializerOptions);
    }

    /**
     * Get configuration options for instantiating a serializer adapter
     *
     * @return mixed[]
     */
    public function getSerializerOptions(): array
    {
        return $this->serializerOptions;
    }
}
