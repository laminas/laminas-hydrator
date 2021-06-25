<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

interface StrategyEnabledInterface
{
    /**
     * Adds the given strategy under the given name.
     */
    public function addStrategy(string $name, StrategyInterface $strategy): void;

    /**
     * Gets the strategy with the given name.
     */
    public function getStrategy(string $name): StrategyInterface;

    /**
     * Checks if the strategy with the given name exists.
     */
    public function hasStrategy(string $name): bool;

    /**
     * Removes the strategy with the given name.
     */
    public function removeStrategy(string $name): void;
}
