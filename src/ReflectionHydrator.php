<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

use ReflectionClass;
use ReflectionProperty;

class ReflectionHydrator extends AbstractHydrator
{
    /**
     * Simple in-memory array cache of ReflectionProperties used.
     *
     * @var ReflectionProperty[][]
     */
    protected static $reflProperties = [];

    /**
     * Extract values from an object
     *
     * {@inheritDoc}
     */
    public function extract(object $object, bool $includeParentProperties = false): array
    {
        $result = [];
        foreach (self::getReflProperties($object, $includeParentProperties) as $property) {
            $propertyName = $this->extractName($property->getName(), $object);
            if (! $this->getCompositeFilter()->filter($propertyName)) {
                continue;
            }

            $value                 = $property->getValue($object);
            $result[$propertyName] = $this->extractValue($propertyName, $value, $object);
        }

        return $result;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * {@inheritDoc}
     */
    public function hydrate(array $data, object $object, bool $includeParentProperties = false)
    {
        $reflProperties = self::getReflProperties($object, $includeParentProperties);
        foreach ($data as $key => $value) {
            $name = $this->hydrateName($key, $data);
            if (isset($reflProperties[$name])) {
                $reflProperties[$name]->setValue($object, $this->hydrateValue($name, $value, $data));
            }
        }
        return $object;
    }

    /**
     * Get a reflection properties from in-memory cache and lazy-load if
     * class has not been loaded.
     *
     * @return ReflectionProperty[]
     */
    protected static function getReflProperties(object $input, bool $includeParentProperties): array
    {
        $reflClass = new ReflectionClass($input);
        $class     = $reflClass->getName();

        if (isset(static::$reflProperties[$class])) {
            return static::$reflProperties[$class];
        }

        static::$reflProperties[$class] = [];

        do {
            foreach ($reflClass->getProperties() as $property) {
                /** @psalm-suppress UnusedMethodCall - Bizarre, this is a void return!!! */
                $property->setAccessible(true);
                static::$reflProperties[$class][$property->getName()] = $property;
            }
        } while ($includeParentProperties === true && ($reflClass = $reflClass->getParentClass()) !== false);

        return static::$reflProperties[$class];
    }
}
