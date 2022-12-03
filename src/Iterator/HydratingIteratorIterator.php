<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Iterator;

use Iterator;
use IteratorIterator;
use Laminas\Hydrator\Exception\InvalidArgumentException;
use Laminas\Hydrator\HydratorInterface;
use ReturnTypeWillChange;

use function class_exists;
use function is_object;
use function sprintf;

/**
 * @template TKey
 * @template TPrototype of object
 * @template TInputData of array
 * @template TIterator of Iterator<TKey, TInputData>
 * @template-extends IteratorIterator<TKey, TInputData, TIterator>
 * @template-implements HydratingIteratorInterface<TKey, TPrototype>
 */
class HydratingIteratorIterator extends IteratorIterator implements HydratingIteratorInterface
{
    /** @var HydratorInterface */
    protected $hydrator;

    /** @var TPrototype */
    protected $prototype;

    /**
     * @param Iterator<TKey, TInputData>          $data
     * @param class-string<TPrototype>|TPrototype $prototype
     */
    public function __construct(HydratorInterface $hydrator, Iterator $data, $prototype)
    {
        $this->setHydrator($hydrator);
        $this->setPrototype($prototype);
        parent::__construct($data);
    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException If $prototype is a string, but refers to
     *     a non-existent class.
     */
    public function setPrototype($prototype): void
    {
        if (is_object($prototype)) {
            $this->prototype = $prototype;
            return;
        }

        if (! class_exists($prototype)) {
            throw new InvalidArgumentException(
                sprintf('Method %s was passed an invalid class name: %s', __METHOD__, $prototype)
            );
        }

        $this->prototype = new $prototype();
    }

    /**
     * @inheritDoc
     */
    public function setHydrator(HydratorInterface $hydrator): void
    {
        $this->hydrator = $hydrator;
    }

    /**
     * @psalm-suppress ImplementedReturnTypeMismatch we are explicitly replacing the type of {@see parent::current()}
     *                 with a hydrated value here, breaking LSP by design.
     * @return TPrototype|null
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        $currentValue = parent::current();
        $object       = clone $this->prototype;
        $this->hydrator->hydrate($currentValue, $object);
        return $object;
    }
}
