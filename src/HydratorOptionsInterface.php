<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

interface HydratorOptionsInterface
{
    /**
     * @param mixed[] $options
     */
    public function setOptions(iterable $options): void;
}
