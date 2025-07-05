<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\Filter\FilterInterface;
use Laminas\Hydrator\Filter\FilterProviderInterface;
use Laminas\Hydrator\Filter\GetFilter;
use Laminas\Hydrator\Filter\MethodMatchFilter;

class ClassMethodsFilterProviderInterface implements FilterProviderInterface
{
    public function getBar(): string
    {
        return "foo";
    }

    public function getFoo(): string
    {
        return "bar";
    }

    /**
     * @param mixed $foo
     */
    public function isScalar($foo): bool
    {
        return false;
    }

    public function hasFooBar(): bool
    {
        return true;
    }

    public function getServiceManager(): string
    {
        return "servicemanager";
    }

    public function getEventManager(): string
    {
        return "eventmanager";
    }

    public function getFilter(): FilterInterface
    {
        $filterComposite = new FilterComposite();

        $filterComposite->addFilter("get", new GetFilter());
        $excludes = new FilterComposite();
        $excludes->addFilter(
            "servicemanager",
            new MethodMatchFilter("getServiceManager"),
            FilterComposite::CONDITION_AND
        );
        $excludes->addFilter(
            "eventmanager",
            new MethodMatchFilter("getEventManager"),
            FilterComposite::CONDITION_AND
        );
        $filterComposite->addFilter("excludes", $excludes, FilterComposite::CONDITION_AND);

        return $filterComposite;
    }
}
