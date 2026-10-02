<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

use AllowDynamicProperties;

#[AllowDynamicProperties]
final class HydratorClosureStrategyEntity
{
    public function __construct(public mixed $field1 = null, public mixed $field2 = null)
    {
    }
}
