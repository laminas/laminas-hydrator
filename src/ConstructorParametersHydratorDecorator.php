<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;

final class ConstructorParametersHydratorDecorator implements HydratorInterface
{
    /** @var array<class-string, ReflectionParameter[]> */
    private static $parametersCache = [];

    /** @var AbstractHydrator */
    private $decoratedHydrator;

    public function __construct(AbstractHydrator $decoratedHydrator)
    {
        $this->decoratedHydrator = $decoratedHydrator;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(object $object): array
    {
        return $this->decoratedHydrator->extract($object);
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, object $object)
    {
        if (! $object instanceof ProxyObject) {
            return $this->decoratedHydrator->hydrate($data, $object);
        }

        $constructorParameters = $this->getConstructorParameters($object);
        $parameters            = [];
        foreach ($constructorParameters as $constructorParameter) {
            $parameterName = $this->decoratedHydrator->extractName($constructorParameter->getName());
            try {
                /** @var mixed $value */
                $value = $data[$parameterName] ?? $constructorParameter->getDefaultValue();
            } catch (ReflectionException $e) {
                $value = null;
            }

            $value        = $this->castScalarValue($value, $constructorParameter);
            $parameters[] = $this->decoratedHydrator->hydrateValue($parameterName, $value, $data);
        }

        return $this->decoratedHydrator->hydrate($data, $object->createProxiedObject($parameters));
    }

    /** @return ReflectionParameter[] */
    private function getConstructorParameters(ProxyObject $object): array
    {
        if (! isset(self::$parametersCache[$object->getObjectClassName()])) {
            $reflection  = new ReflectionClass($object->getObjectClassName());
            $constructor = $reflection->getConstructor();

            self::$parametersCache[$object->getObjectClassName()] = [];
            if ($constructor !== null) {
                self::$parametersCache[$object->getObjectClassName()] = $constructor->getParameters();
            }
        }

        return self::$parametersCache[$object->getObjectClassName()];
    }

    /**
     * @param mixed $value
     * @param ReflectionParameter $constructorParameter
     * @return mixed
     */
    private function castScalarValue($value, ReflectionParameter $constructorParameter)
    {
        if ($value === null || !$constructorParameter->getType() instanceof ReflectionNamedType) {
            return $value;
        }

        switch ($constructorParameter->getType()->getName()) {
            case 'string':
                return (string)$value;
            case 'int':
                return (int)$value;
            case 'float':
                return (float)$value;
            case 'bool':
                return (bool)$value;
            default:
                return $value;
        }
    }
}