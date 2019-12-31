<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Hydrator;

class ConfigProvider
{
    /**
     * Return configuration for this component.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Return dependency mappings for this component.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'aliases' => [
                'HydratorManager' => HydratorPluginManager::class,

                // Legacy Zend Framework aliases
                \Zend\Hydrator\HydratorPluginManager::class => HydratorPluginManager::class,
            ],
            'factories' => [
                HydratorPluginManager::class => HydratorPluginManagerFactory::class,
            ],
        ];
    }
}
