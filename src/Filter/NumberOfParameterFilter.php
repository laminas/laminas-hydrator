<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Filter;

use Laminas\Hydrator\Exception\InvalidArgumentException;
use ReflectionException;
use ReflectionMethod;

use function sprintf;

final class NumberOfParameterFilter implements FilterInterface
{
    /**
     * @param int $numberOfParameters Number of accepted parameters
     */
    public function __construct(
        /**
         * The number of parameters being accepted
         */
        private int $numberOfParameters = 0
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function filter(string $property, ?object $instance = null): bool
    {
        try {
            $reflectionMethod = $instance !== null
                ? new ReflectionMethod($instance, $property)
                : new ReflectionMethod($property);
        } catch (ReflectionException) {
            throw new InvalidArgumentException(sprintf(
                'Method %s does not exist',
                $property
            ));
        }

        return $reflectionMethod->getNumberOfParameters() === $this->numberOfParameters;
    }
}
