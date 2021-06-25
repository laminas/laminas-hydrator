<?php

declare(strict_types=1);

namespace Laminas\Hydrator\NamingStrategy;

final class IdentityNamingStrategy implements NamingStrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function hydrate(string $name, ?array $data = null): string
    {
        return $name;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(string $name, ?object $object = null): string
    {
        return $name;
    }
}
