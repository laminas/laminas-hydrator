<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Aggregate;

use Laminas\EventManager\Event;

/**
 * Event triggered when the {@see AggregateHydrator} hydrates
 * data into an object
 *
 * @template T of object
 */
class HydrateEvent extends Event
{
    public const EVENT_HYDRATE = 'hydrate';

    /**
     * {@inheritDoc}
     */
    protected $name = self::EVENT_HYDRATE;

    /**
     * @var object
     * @psalm-var T
     */
    protected $hydratedObject;

    /** @var mixed[] Data being used to hydrate the $hydratedObject */
    protected $hydrationData;

    /**
     * @param mixed[] $hydrationData Data being used to hydrate the $hydratedObject
     * @psalm-param T $hydratedObject
     */
    public function __construct(object $target, object $hydratedObject, array $hydrationData)
    {
        parent::__construct();
        $this->target         = $target;
        $this->hydratedObject = $hydratedObject;
        $this->hydrationData  = $hydrationData;
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
