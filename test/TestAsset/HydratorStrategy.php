<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Hydrator\TestAsset;

use Laminas\Hydrator\Strategy\DefaultStrategy;

class HydratorStrategy extends DefaultStrategy
{
    /**
     * A simulated storage device which is just an array with Car objects.
     *
     * @var array
     */
    private $simulatedStorageDevice;

    public function __construct()
    {
        $this->simulatedStorageDevice = [];
        $this->simulatedStorageDevice[] = new HydratorStrategyEntityB(111, 'AAA');
        $this->simulatedStorageDevice[] = new HydratorStrategyEntityB(222, 'BBB');
        $this->simulatedStorageDevice[] = new HydratorStrategyEntityB(333, 'CCC');
    }

    public function extract($value)
    {
        $result = [];
        foreach ($value as $instance) {
            $result[] = $instance->getField1();
        }
        return $result;
    }

    public function hydrate($value)
    {
        $result = $value;
        if (is_array($value)) {
            $result = [];
            foreach ($value as $field1) {
                $result[] = $this->findEntity($field1);
            }
        }
        return $result;
    }

    private function findEntity($field1)
    {
        $result = null;
        foreach ($this->simulatedStorageDevice as $entity) {
            if ($entity->getField1() == $field1) {
                $result = $entity;
                break;
            }
        }
        return $result;
    }
}
