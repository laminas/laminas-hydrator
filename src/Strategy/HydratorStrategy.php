<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Hydrator\HydratorInterface;
use ReflectionClass;
use ReflectionException;

use function class_exists;
use function get_debug_type;
use function is_array;
use function sprintf;

final class HydratorStrategy implements StrategyInterface
{
    private readonly string $objectClassName;

    /**
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(
        private readonly HydratorInterface $objectHydrator,
        string $objectClassName
    ) {
        if (! class_exists($objectClassName)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Object class name needs to be the name of an existing class, got "%s" instead.',
                    $objectClassName
                )
            );
        }
        $this->objectClassName = $objectClassName;
    }

    /**
     * @param object      $value  The original value.
     * @param null|object $object (optional) The original object for context.
     * @return mixed Returns the value that should be extracted.
     * @throws Exception\InvalidArgumentException
     */
    public function extract($value, ?object $object = null): array
    {
        if (! $value instanceof $this->objectClassName) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Value needs to be an instance of "%s", got "%s" instead.',
                    $this->objectClassName,
                    get_debug_type($value)
                )
            );
        }

        return $this->objectHydrator->extract($value);
    }

    /**
     * @param mixed      $value The original value.
     * @param null|array $data  (optional) The original data for context.
     * @throws ReflectionException
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(mixed $value, ?array $data = null): object|string|null
    {
        if (
            $value === ''
            || $value === null
            || $value instanceof $this->objectClassName
        ) {
            return $value;
        }

        if (! is_array($value)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Value needs to be an array, got "%s" instead.',
                    get_debug_type($value)
                )
            );
        }

        $reflection = new ReflectionClass($this->objectClassName);

        return $this->objectHydrator->hydrate(
            $value,
            $reflection->newInstanceWithoutConstructor()
        );
    }
}
