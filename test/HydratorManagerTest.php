<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\HydratorPluginManager;

class HydratorManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HydratorPluginManager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new HydratorPluginManager();
    }

    public function testRegisteringInvalidElementRaisesException()
    {
        $this->setExpectedException('Laminas\Hydrator\Exception\RuntimeException');
        $this->manager->setService('test', $this);
    }

    public function testLoadingInvalidElementRaisesException()
    {
        $this->manager->setInvokableClass('test', get_class($this));
        $this->setExpectedException('Laminas\Hydrator\Exception\RuntimeException');
        $this->manager->get('test');
    }
}
