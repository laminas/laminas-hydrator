<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use function get_object_vars;

class HydratorStrategyEntityA
{
    public $entities; // public to make testing easier!

    public function __construct()
    {
        $this->entities = [];
    }

    public function addEntity(HydratorStrategyEntityB $entity)
    {
        $this->entities[] = $entity;
    }

    public function getEntities()
    {
        return $this->entities;
    }

    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    // Add the getArrayCopy method so we can test the ArraySerializable hydrator:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    // Add the populate method so we can test the ArraySerializable hydrator:
    public function populate($data)
    {
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }
}
