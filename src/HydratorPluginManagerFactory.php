<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function assert;
use function is_array;

/**
 * @internal
 *
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 */
final class HydratorPluginManagerFactory
{
    public function __invoke(ContainerInterface $container): HydratorPluginManager
    {
        $config = $container->has('config')
            ? $container->get('config')
            : [];
        assert(is_array($config));

        /** @psalm-var ServiceManagerConfiguration $hydrators */
        $hydrators = isset($config['hydrators']) && is_array($config['hydrators'])
            ? $config['hydrators']
            : [];

        return new HydratorPluginManager($container, $hydrators);
    }
}
