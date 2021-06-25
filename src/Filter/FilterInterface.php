<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Filter;

interface FilterInterface
{
    /**
     * Should return true, if the given filter does not match
     *
     * Filters may take an optional second parameter, typed as a null or an
     * object. When present, it represents the object instance on which the
     * property should exist. For an example, see the ClassMethodsHydrator,
     * and the NumberOfParameterFilter.
     *
     * @param string $property The name of the property
     */
    public function filter(string $property, ?object $instance = null): bool;
}
