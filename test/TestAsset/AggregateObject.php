<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

/**
 * Test asset to verify that a composition of a class-methods and an array-serializable
 * hydrator produces the expected output
 */
final class AggregateObject
{
    public array $arrayData = ['president' => 'Zaphod'];

    public string $maintainer = 'Marvin';

    public function getMaintainer(): string
    {
        return $this->maintainer;
    }

    public function setMaintainer(string $maintainer): void
    {
        $this->maintainer = $maintainer;
    }

    public function getArrayCopy(): array
    {
        return $this->arrayData;
    }

    public function exchangeArray(array $data): void
    {
        $this->arrayData = $data;
    }
}
