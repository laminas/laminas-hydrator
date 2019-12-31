<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Hydrator;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class HydratorPluginManagerFactory implements FactoryInterface
{
    /**
     * laminas-servicemanager v2 support for invocation options.
     *
     * @param array
     */
    protected $creationOptions;

    /**
     * {@inheritDoc}
     *
     * @return HydratorPluginManager
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        return new HydratorPluginManager($container, $options ?: []);
    }

    /**
     * {@inheritDoc}
     *
     * @return HydratorPluginManager
     */
    public function createService(ServiceLocatorInterface $container, $name = null, $requestedName = null)
    {
        return $this($container, $requestedName ?: HydratorPluginManager::class, $this->creationOptions);
    }

    /**
     * laminas-servicemanager v2 support for invocation options.
     *
     * @param array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->creationOptions = $options;
    }
}
