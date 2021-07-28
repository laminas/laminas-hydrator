<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

interface HydrationInterface
{
    /**
     * Hydrate $object with the provided $data.
     *
     * @param mixed[] $data
     * @return object The implementation should return an object of any type.
     *     By purposely omitting the return type from the signature,
     *     implementations may choose to specify a more specific type.
     * @psalm-param T $object
     * @psalm-return T
     * @template T of object
     */
    public function hydrate(array $data, object $object);
}
