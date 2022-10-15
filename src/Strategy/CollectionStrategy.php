<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Hydrator\Exception;
use Laminas\Hydrator\HydratorInterface;
use ReflectionClass;

use function array_map;
use function class_exists;
use function gettype;
use function is_array;
use function is_object;
use function sprintf;

class CollectionStrategy implements StrategyInterface
{
    private string $objectClassName;

    /**
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(private HydratorInterface $objectHydrator, string $objectClassName)
    {
        if (! class_exists($objectClassName)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Object class name needs to be the name of an existing class, got "%s" instead.',
                $objectClassName
            ));
        }
        $this->objectClassName = $objectClassName;
    }

    /**
     * Converts the given value so that it can be extracted by the hydrator.
     *
     * @param  mixed[] $value The original value.
     * @throws Exception\InvalidArgumentException
     * @return mixed Returns the value that should be extracted.
     */
    public function extract($value, ?object $object = null)
    {
        if (! is_array($value)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Value needs to be an array, got "%s" instead.',
                is_object($value) ? $value::class : gettype($value)
            ));
        }

        return array_map(function ($object): array {
            if (! $object instanceof $this->objectClassName) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Value needs to be an instance of "%s", got "%s" instead.',
                    $this->objectClassName,
                    is_object($object) ? $object::class : gettype($object)
                ));
            }

            return $this->objectHydrator->extract($object);
        }, $value);
    }

    /**
     * Converts the given value so that it can be hydrated by the hydrator.
     *
     * @param  mixed[] $value The original value.
     * @throws Exception\InvalidArgumentException
     * @return object[] Returns the value that should be hydrated.
     */
    public function hydrate($value, ?array $data = null)
    {
        if (! is_array($value)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Value needs to be an array, got "%s" instead.',
                is_object($value) ? $value::class : gettype($value)
            ));
        }

        $reflection = new ReflectionClass($this->objectClassName);

        return array_map(fn($data): object => $this->objectHydrator->hydrate(
            $data,
            $reflection->newInstanceWithoutConstructor()
        ), $value);
    }
}
