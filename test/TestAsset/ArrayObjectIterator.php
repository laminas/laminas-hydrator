<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use Iterator;

use function current;
use function is_array;
use function key;
use function next;
use function reset;

class ArrayObjectIterator implements Iterator
{
    private array $var = [];

    /** @param mixed $array */
    public function __construct($array)
    {
        if (is_array($array)) {
            $this->var = $array;
        }
    }

    public function rewind(): void
    {
        reset($this->var);
    }

    public function current(): mixed
    {
        return current($this->var);
    }

    /** @return int|string */
    public function key(): mixed
    {
        return key($this->var);
    }

    public function next(): void
    {
        next($this->var);
    }

    public function valid(): bool
    {
        $key = key($this->var);
        return $key !== null && $key !== false;
    }
}
