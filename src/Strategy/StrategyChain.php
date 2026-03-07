<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Stdlib\ArrayUtils;

use function array_map;
use function array_reverse;

final class StrategyChain implements StrategyInterface
{
    /**
     * Strategy chain for extraction
     *
     * @var StrategyInterface[]
     */
    private readonly array $extractionStrategies;

    /**
     * Strategy chain for hydration
     *
     * @var StrategyInterface[]
     */
    private readonly array $hydrationStrategies;

    /**
     * @param StrategyInterface[] $extractionStrategies
     */
    public function __construct(iterable $extractionStrategies)
    {
        $extractionStrategies       = ArrayUtils::iteratorToArray($extractionStrategies);
        $this->extractionStrategies = array_map(
            // this callback is here only to ensure type-safety
            static fn(StrategyInterface $strategy): StrategyInterface => $strategy,
            $extractionStrategies
        );

        $this->hydrationStrategies = array_reverse($extractionStrategies);
    }

    /**
     * {@inheritDoc}
     */
    public function extract(mixed $value, ?object $object = null): mixed
    {
        foreach ($this->extractionStrategies as $strategy) {
            $value = $strategy->extract($value, $object);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(mixed $value, ?array $data = null): mixed
    {
        foreach ($this->hydrationStrategies as $strategy) {
            $value = $strategy->hydrate($value, $data);
        }

        return $value;
    }
}
