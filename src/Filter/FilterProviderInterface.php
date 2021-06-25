<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Filter;

interface FilterProviderInterface
{
    /**
     * Provides a filter for hydration
     */
    public function getFilter(): FilterInterface;
}
