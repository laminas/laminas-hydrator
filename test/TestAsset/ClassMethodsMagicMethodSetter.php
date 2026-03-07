<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use function strlen;
use function strtolower;
use function substr;

class ClassMethodsMagicMethodSetter
{
    protected mixed $foo;

    public function __call(string $method, array $args)
    {
        if (strlen($method) > 3 && strtolower(substr($method, 3)) === 'foo') {
            $this->foo = $args[0];
        }
    }

    public function getFoo(): mixed
    {
        return $this->foo ?? null;
    }
}
