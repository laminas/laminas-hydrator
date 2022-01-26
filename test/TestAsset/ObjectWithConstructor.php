<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

final class ObjectWithConstructor
{
    /** @var string */
    private $foo;

    /** @var int|null */
    private $bar;

    /** @var bool */
    private $isMandatory;

    /** @var string|null */
    private $baz;

    /** @codingStandardsIgnoreStart */
    public function __construct(string $foo, ?int $bar = 42, bool $isMandatory)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->isMandatory = $isMandatory;
    }
    /** @codingStandardsIgnoreEnd */

    public function getFoo(): string
    {
        return $this->foo;
    }

    public function getBar(): ?int
    {
        return $this->bar;
    }

    public function isMandatory(): bool
    {
        return $this->isMandatory;
    }

    public function getBaz(): ?string
    {
        return $this->baz;
    }

    public function setBaz(?string $baz): self
    {
        $this->baz = $baz;
        return $this;
    }
}
