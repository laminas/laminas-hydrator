<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Hydrator\Exception;
use Laminas\Hydrator\HydratorInterface;
use ReflectionClass;

use function array_map;
use function class_exists;
use function get_debug_type;
use function is_array;
use function sprintf;

/**
 * @template T of object
 */
final class CollectionStrategy implements StrategyInterface
{
    /**
     * @param class-string<T> $objectClassName
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(
        private readonly HydratorInterface $objectHydrator,
        private readonly string $objectClassName
    ) {
        if (! class_exists($this->objectClassName)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Object class name needs to be the name of an existing class, got "%s" instead.',
                $this->objectClassName
            ));
        }
    }

    /**
     * Converts the given value so that it can be extracted by the hydrator.
     *
     * @param  array<array-key, T> $value The original value.
     * @throws Exception\InvalidArgumentException
     * @return mixed Returns the value that should be extracted.
     */
    public function extract(mixed $value, ?object $object = null): array
    {
        if (! is_array($value)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Value needs to be an array, got "%s" instead.',
                get_debug_type($value)
            ));
        }

        return array_map(function (object $object): array {
            if (! $object instanceof $this->objectClassName) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Value needs to be an instance of "%s", got "%s" instead.',
                    $this->objectClassName,
                    get_debug_type($object)
                ));
            }

            return $this->objectHydrator->extract($object);
        }, $value);
    }

    /**
     * Converts the given value so that it can be hydrated by the hydrator.
     *
     * @param mixed[] $value The original value.
     * @throws Exception\InvalidArgumentException
     * @return array<array-key, T> Returns the value that should be hydrated.
     */
    public function hydrate($value, ?array $data = null): array
    {
        if (! is_array($value)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Value needs to be an array, got "%s" instead.',
                get_debug_type($value)
            ));
        }

        $reflection = new ReflectionClass($this->objectClassName);

        return array_map(fn($data): object => $this->objectHydrator->hydrate(
            $data,
            $reflection->newInstanceWithoutConstructor()
        ), $value);
    }
}
