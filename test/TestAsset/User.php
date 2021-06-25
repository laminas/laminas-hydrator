<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

final class User
{
    /** @var string */
    private $name;

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
}
