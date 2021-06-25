<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

use Psr\Container\ContainerInterface;

final class StandaloneHydratorPluginManagerFactory
{
    public function __invoke(ContainerInterface $container): StandaloneHydratorPluginManager
    {
        return new StandaloneHydratorPluginManager();
    }
}
