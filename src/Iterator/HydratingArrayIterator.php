<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Iterator;

use ArrayIterator;
use Laminas\Hydrator\HydratorInterface;

class HydratingArrayIterator extends HydratingIteratorIterator
{
    /**
     * @param mixed[]       $data Data being used to hydrate the $prototype
     * @param string|object $prototype Object, or class name to use for prototype.
     */
    public function __construct(HydratorInterface $hydrator, array $data, $prototype)
    {
        parent::__construct($hydrator, new ArrayIterator($data), $prototype);
    }
}
