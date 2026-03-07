<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Hydrator\Exception\InvalidArgumentException;

use function get_debug_type;
use function is_bool;
use function is_int;
use function is_string;
use function sprintf;

/**
 * This Strategy extracts and hydrates int and string values to Boolean values
 */
final class BooleanStrategy implements StrategyInterface
{
    public function __construct(private readonly int|string $trueValue, private readonly int|string $falseValue)
    {
    }

    /**
     * Converts the given value so that it can be extracted by the hydrator.
     *
     * @param bool $value The original value.
     * @throws InvalidArgumentException
     * @return int|string Returns the value that should be extracted.
     */
    public function extract(mixed $value, ?object $object = null): int|string
    {
        if (! is_bool($value)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to extract. Expected bool. %s was given.',
                get_debug_type($value)
            ));
        }

        return $value ? $this->trueValue : $this->falseValue;
    }

    /**
     * Converts the given value so that it can be hydrated by the hydrator.
     *
     * @param  bool|int|string $value The original value.
     * @throws InvalidArgumentException
     * @return bool Returns the value that should be hydrated.
     */
    public function hydrate(mixed $value, ?array $data = null): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (! is_string($value) && ! is_int($value)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to hydrate. Expected bool, string or int. %s was given.',
                get_debug_type($value)
            ));
        }

        if ($value === $this->trueValue) {
            return true;
        }

        if ($value === $this->falseValue) {
            return false;
        }

        throw new InvalidArgumentException(sprintf(
            'Unexpected value %s can\'t be hydrated. Expect %s or %s as Value.',
            $value,
            $this->trueValue,
            $this->falseValue
        ));
    }
}
