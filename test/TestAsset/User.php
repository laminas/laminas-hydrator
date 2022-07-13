<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

final class User
{
    private ?string $name = null;

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
}
