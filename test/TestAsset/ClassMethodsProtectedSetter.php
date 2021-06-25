<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsProtectedSetter
{
    /** @var mixed */
    protected $foo;

    /** @var mixed */
    protected $bar;

    /** @param mixed $foo */
    protected function setFoo($foo): void
    {
        $this->foo = $foo;
    }

    /** @param mixed $bar */
    public function setBar($bar): void
    {
        $this->bar = $bar;
    }

    /** @return mixed */
    public function getBar()
    {
        return $this->bar;
    }
}
