<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ProxyObject;
use ReflectionClass;
use ReflectionException;

use function class_exists;
use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function sprintf;

class HydratorStrategy implements StrategyInterface
{
    /** @var HydratorInterface */
    private $objectHydrator;

    /** @var string */
    private $objectClassName;

    /** @var bool */
    private $useProxyObject;

    /**
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(
        HydratorInterface $objectHydrator,
        string $objectClassName,
        bool $useProxyObject = false
    ) {
        if (! class_exists($objectClassName)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Object class name needs to be the name of an existing class, got "%s" instead.',
                    $objectClassName
                )
            );
        }

        $this->objectHydrator  = $objectHydrator;
        $this->objectClassName = $objectClassName;
        $this->useProxyObject  = $useProxyObject;
    }

    /**
     * @param object      $value  The original value.
     * @param null|object $object (optional) The original object for context.
     * @return mixed Returns the value that should be extracted.
     * @throws Exception\InvalidArgumentException
     */
    public function extract($value, ?object $object = null)
    {
        if (! $value instanceof $this->objectClassName) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Value needs to be an instance of "%s", got "%s" instead.',
                    $this->objectClassName,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        return $this->objectHydrator->extract($value);
    }

    /**
     * @param mixed      $value The original value.
     * @param null|array $data  (optional) The original data for context.
     * @return object|string|null
     * @throws ReflectionException
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate($value, ?array $data = null)
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
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        if ($this->useProxyObject) {
            return new ProxyObject($this->objectClassName);
        }

        $reflection = new ReflectionClass($this->objectClassName);

        return $this->objectHydrator->hydrate(
            $value,
            $reflection->newInstanceWithoutConstructor()
        );
    }
}
