<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @requires PHP 5.4
 */
class HydratorAwareTraitTest extends TestCase
{
    public function testSetHydrator()
    {
        $object = $this->getObjectForTrait('\Laminas\Hydrator\HydratorAwareTrait');

        $this->assertAttributeEquals(null, 'hydrator', $object);

        $hydrator = $this->getMockForAbstractClass('\Laminas\Hydrator\AbstractHydrator');

        $object->setHydrator($hydrator);

        $this->assertAttributeEquals($hydrator, 'hydrator', $object);
    }

    public function testGetHydrator()
    {
        $object = $this->getObjectForTrait('\Laminas\Hydrator\HydratorAwareTrait');

        $this->assertNull($object->getHydrator());

        $hydrator = $this->getMockForAbstractClass('\Laminas\Hydrator\AbstractHydrator');

        $object->setHydrator($hydrator);

        $this->assertEquals($hydrator, $object->getHydrator());
    }
}
