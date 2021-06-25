<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use function strlen;
use function strtolower;
use function substr;

class ClassMethodsMagicMethodSetter
{
    /** @var mixed */
    protected $foo;

    /**
     * @param string $method
     * @param array $args
     */
    public function __call($method, $args)
    {
        if (strlen($method) > 3 && strtolower(substr($method, 3)) === 'foo') {
            $this->foo = $args[0];
        }
    }

    /** @return mixed */
    public function getFoo()
    {
        return $this->foo;
    }
}
