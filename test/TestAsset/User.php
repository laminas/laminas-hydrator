<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

final class User
{
    private ?string $name = null;

    public function name(): ?string
    {
        return $this->name;
    }
}
