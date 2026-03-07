<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

use Psr\Container\ContainerInterface;

final class DelegatingHydrator implements HydratorInterface
{
    public function __construct(private readonly ContainerInterface $hydrators)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hydrate(array $data, object $object): object
    {
        return $this->getHydrator($object)->hydrate($data, $object);
    }

    /**
     * {@inheritdoc}
     */
    public function extract(object $object): array
    {
        return $this->getHydrator($object)->extract($object);
    }

    /**
     * Gets hydrator for an object
     */
    private function getHydrator(object $object): HydratorInterface
    {
        return $this->hydrators->get($object::class);
    }
}
