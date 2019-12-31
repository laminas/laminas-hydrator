<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator;

use Interop\Container\ContainerInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\Hydrator\HydratorPluginManagerFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit_Framework_TestCase as TestCase;

class HydratorPluginManagerFactoryTest extends TestCase
{
    public function testFactoryReturnsPluginManager()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $factory = new HydratorPluginManagerFactory();

        $hydrators = $factory($container, HydratorPluginManagerFactory::class);
        $this->assertInstanceOf(HydratorPluginManager::class, $hydrators);

        if (method_exists($hydrators, 'configure')) {
            // laminas-servicemanager v3
            $this->assertAttributeSame($container, 'creationContext', $hydrators);
        } else {
            // laminas-servicemanager v2
            $this->assertSame($container, $hydrators->getServiceLocator());
        }
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderContainerInterop()
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $hydrator = $this->prophesize(HydratorInterface::class)->reveal();

        $factory = new HydratorPluginManagerFactory();
        $hydrators = $factory($container, HydratorPluginManagerFactory::class, [
            'services' => [
                'test' => $hydrator,
            ],
        ]);
        $this->assertSame($hydrator, $hydrators->get('test'));
    }

    /**
     * @depends testFactoryReturnsPluginManager
     */
    public function testFactoryConfiguresPluginManagerUnderServiceManagerV2()
    {
        $container = $this->prophesize(ServiceLocatorInterface::class);
        $container->willImplement(ContainerInterface::class);

        $hydrator = $this->prophesize(HydratorInterface::class)->reveal();

        $factory = new HydratorPluginManagerFactory();
        $factory->setCreationOptions([
            'services' => [
                'test' => $hydrator,
            ],
        ]);

        $hydrators = $factory->createService($container->reveal());
        $this->assertSame($hydrator, $hydrators->get('test'));
    }
}
