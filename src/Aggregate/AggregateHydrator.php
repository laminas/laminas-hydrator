<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Aggregate;

use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Hydrator\HydratorInterface;

use function assert;

/**
 * Aggregate hydrator that composes multiple hydrators via events
 */
final class AggregateHydrator implements HydratorInterface, EventManagerAwareInterface
{
    public const DEFAULT_PRIORITY = 1;

    private ?EventManagerInterface $eventManager = null;

    /**
     * Attaches the provided hydrator to the list of hydrators to be used while hydrating/extracting data
     */
    public function add(HydratorInterface $hydrator, int $priority = self::DEFAULT_PRIORITY): void
    {
        $listener = new HydratorListener($hydrator);
        $listener->attach($this->getEventManager(), $priority);
    }

    /**
     * {@inheritDoc}
     */
    public function extract(object $object): array
    {
        $event = new ExtractEvent($this, $object);
        $this->getEventManager()->triggerEvent($event);
        return $event->getExtractedData();
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate(array $data, object $object): object
    {
        $event = new HydrateEvent($this, $object, $data);
        $this->getEventManager()->triggerEvent($event);
        return $event->getHydratedObject();
    }

    /**
     * {@inheritDoc}
     */
    public function setEventManager(EventManagerInterface $eventManager): void
    {
        $eventManager->setIdentifiers([self::class, self::class]);
        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getEventManager(): EventManagerInterface
    {
        if (! $this->eventManager instanceof EventManagerInterface) {
            $this->setEventManager(new EventManager());
            assert($this->eventManager instanceof EventManagerInterface);
        }

        return $this->eventManager;
    }
}
