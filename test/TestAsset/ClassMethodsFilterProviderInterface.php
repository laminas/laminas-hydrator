<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\TestAsset;

use Laminas\Hydrator\Filter\FilterComposite;
use Laminas\Hydrator\Filter\FilterProviderInterface;
use Laminas\Hydrator\Filter\GetFilter;
use Laminas\Hydrator\Filter\MethodMatchFilter;

class ClassMethodsFilterProviderInterface implements FilterProviderInterface
{
    public function getBar()
    {
        return "foo";
    }

    public function getFoo()
    {
        return "bar";
    }

    public function isScalar($foo)
    {
        return false;
    }

    public function hasFooBar()
    {
        return true;
    }

    public function getServiceManager()
    {
        return "servicemanager";
    }

    public function getEventManager()
    {
        return "eventmanager";
    }

    public function getFilter()
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
