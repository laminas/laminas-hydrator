<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

use Laminas\ServiceManager\ServiceManager;

use function class_exists;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final class ConfigProvider
{
    /**
     * Return configuration for this component.
     *
     * @return array{dependencies: ServiceManagerConfiguration}
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Return dependency mappings for this component.
     *
     * If laminas-servicemanager is installed, this will alias the HydratorPluginManager
     * to the `HydratorManager` service; otherwise, it aliases the
     * StandaloneHydratorPluginManager.
     *
     * @return ServiceManagerConfiguration
     */
    public function getDependencyConfig(): array
    {
        $hydratorManagerTarget = class_exists(ServiceManager::class)
            ? HydratorPluginManager::class
            : StandaloneHydratorPluginManager::class;

        return [
            'aliases'   => [
                'HydratorManager' => $hydratorManagerTarget,
            ],
            'factories' => [
                HydratorPluginManager::class           => HydratorPluginManagerFactory::class,
                StandaloneHydratorPluginManager::class => StandaloneHydratorPluginManagerFactory::class,
            ],
        ];
    }
}
