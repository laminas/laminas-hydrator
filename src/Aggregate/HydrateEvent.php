<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Aggregate;

use Laminas\EventManager\Event;

/**
 * Event triggered when the {@see AggregateHydrator} hydrates
 * data into an object
 *
 * @template T of object
 * @template TTarget of object
 * @extends Event<TTarget, array<empty, empty>>
 * @final
 */
class HydrateEvent extends Event
{
    public const EVENT_HYDRATE = 'hydrate';

    /**
     * {@inheritDoc}
     */
    protected $name = self::EVENT_HYDRATE;

    /**
     * @param mixed[] $hydrationData Data being used to hydrate the $hydratedObject
     * @psalm-param TTarget $target
     * @psalm-param T $hydratedObject
     */
    public function __construct(object $target, protected object $hydratedObject, protected array $hydrationData)
    {
        parent::__construct(self::EVENT_HYDRATE, $target, []);
    }

    /**
     * Retrieves the object that is being hydrated
     *
     * @psalm-return T
     */
    public function getHydratedObject(): object
    {
        return $this->hydratedObject;
    }

    /**
     * @psalm-param T $hydratedObject
     */
    public function setHydratedObject(object $hydratedObject): void
    {
        $this->hydratedObject = $hydratedObject;
    }

    /**
     * Retrieves the data that is being used for hydration
     *
     * @return mixed[]
     */
    public function getHydrationData(): array
    {
        return $this->hydrationData;
    }

    /**
     * @param mixed[] $hydrationData
     */
    public function setHydrationData(array $hydrationData): void
    {
        $this->hydrationData = $hydrationData;
    }
}
