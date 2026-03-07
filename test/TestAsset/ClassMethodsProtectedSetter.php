<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsProtectedSetter
{
    protected mixed $foo;

    protected mixed $bar;

    protected function setFoo(mixed $foo): void
    {
        $this->foo = $foo;
    }

    public function setBar(mixed $bar): void
    {
        $this->bar = $bar;
    }

    public function getBar(): mixed
    {
        return $this->bar;
    }
}
