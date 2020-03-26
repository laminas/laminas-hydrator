<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

/**
 * Test asset to check that type declarations use implicit casting (no strict_types)
 */
class ClassMethodsTypeDeclaration
{
    /**
     * @var int
     */
    public $int;

    public function getInt() : int
    {
        return $this->int;
    }

    public function setInt(int $int) : void
    {
        $this->int = $int;
    }
}
