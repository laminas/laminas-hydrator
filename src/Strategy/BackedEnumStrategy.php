<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use BackedEnum;
use Laminas\Hydrator\Strategy\Exception\InvalidArgumentException;
use ValueError;

use function get_debug_type;
use function is_int;
use function is_string;
use function sprintf;

/**
 * @template T of BackedEnum
 */
final class BackedEnumStrategy implements StrategyInterface
{
    /** @var class-string<T> */
    private string $enumClass;

    /**
     * @param class-string<T> $enumClass
     */
    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    /**
     * @inheritDoc
     */
    public function extract($value, ?object $object = null)
    {
        if (! $value instanceof $this->enumClass) {
            throw new InvalidArgumentException(sprintf(
                "Value must be a %s; %s provided",
                $this->enumClass,
                get_debug_type($value)
            ));
        }

        return $value->value;
    }

    /**
     * @param mixed $value
     * @return T
     */
    public function hydrate($value, ?array $data)
    {
        if ($value instanceof $this->enumClass) {
            return $value;
        }

        if (! (is_int($value) || is_string($value))) {
            throw new InvalidArgumentException(sprintf(
                "Value must be string or int; %s provided",
                get_debug_type($value)
            ));
        }

        try {
            return $this->enumClass::from($value);
        } catch (ValueError $error) {
            throw new InvalidArgumentException(sprintf(
                "Value '%s' is not a valid scalar value for %s",
                (string) $value,
                $this->enumClass
            ), 0, $error);
        }
    }
}
