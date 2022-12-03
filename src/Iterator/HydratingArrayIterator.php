<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Iterator;

use ArrayIterator;
use Laminas\Hydrator\HydratorInterface;

/**
 * @template TKey of array-key
 * @template TPrototype of object
 * @template TInputData of array
 * @template TIterator of ArrayIterator<TKey, TInputData>
 * @template-extends HydratingIteratorIterator<TKey, TPrototype, TInputData, TIterator>
 */
class HydratingArrayIterator extends HydratingIteratorIterator
{
    /**
     * @param array<TKey, TInputData>             $data Data being used to hydrate the $prototype
     * @param class-string<TPrototype>|TPrototype $prototype
     */
    public function __construct(HydratorInterface $hydrator, array $data, $prototype)
    {
        parent::__construct($hydrator, new ArrayIterator($data), $prototype);
    }
}
