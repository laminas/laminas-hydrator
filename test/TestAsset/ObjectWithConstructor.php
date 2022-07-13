<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

final class ObjectWithConstructor
{
    /** @var string */
    private $foo;

    /** @var bool */
    private $isMandatory;

    /** @var float */
    private $price;

    /** @var int|null */
    private $bar;

    /** @var string|null */
    private $baz;

    public function __construct(string $foo, bool $isMandatory, float $price, ?int $bar = 42)
    {
        $this->foo         = $foo;
        $this->isMandatory = $isMandatory;
        $this->bar         = $bar;
        $this->price       = $price;
    }

    public function getFoo(): string
    {
        return $this->foo;
    }

    public function isMandatory(): bool
    {
        return $this->isMandatory;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getBar(): ?int
    {
        return $this->bar;
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
