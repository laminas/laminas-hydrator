<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

final class ProxyObject
{
    /** @var class-string */
    private $objectClassName;

    /** @param class-string $objectClassName */
    public function __construct(string $objectClassName)
    {
        $this->objectClassName = $objectClassName;
    }

    /** @return class-string */
    public function getObjectClassName(): string
    {
        return $this->objectClassName;
    }

    /** @param array<int, mixed> $parameters */
    public function createProxiedObject(array $parameters): object
    {
        return new $this->objectClassName(...$parameters);
    }
}
