<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use Laminas\Hydrator\HydratorAwareInterface;
use Laminas\Hydrator\HydratorAwareTrait;

final class HydratorAwareTraitImplementor implements HydratorAwareInterface
{
    use HydratorAwareTrait;
}
