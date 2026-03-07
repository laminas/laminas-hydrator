<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Laminas\Hydrator\Strategy\DateTimeFormatterStrategy;

final class DateTimeImmutableFormatterStrategy implements StrategyInterface
{
    public function __construct(private readonly DateTimeFormatterStrategy $dateTimeStrategy)
    {
    }

    /**
     * {@inheritDoc}
     *
     * Converts to date time string
     *
     * @param mixed|DateTimeInterface $value
     * @return mixed|string If a non-DateTimeInterface $value is provided, it
     *     will be returned unmodified; otherwise, it will be extracted to a
     *     string.
     */
    public function extract(mixed $value, ?object $object = null): mixed
    {
        return $this->dateTimeStrategy->extract($value, $object);
    }

    /**
     * Converts date time string to DateTimeImmutable instance for injecting to object
     *
     * {@inheritDoc}
     *
     * @param mixed|string $value
     * @return mixed|DateTimeImmutable
     * @throws Exception\InvalidArgumentException If $value is not null, not a
     *     string, nor a DateTimeInterface.
     */
    public function hydrate(mixed $value, ?array $data = null): mixed
    {
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        $hydrated = $this->dateTimeStrategy->hydrate($value, $data);

        if ($hydrated instanceof DateTime) {
            return DateTimeImmutable::createFromMutable($hydrated);
        }

        return $hydrated ? : $value;
    }
}
