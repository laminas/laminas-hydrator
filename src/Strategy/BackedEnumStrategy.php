<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use BackedEnum;
use Laminas\Hydrator\Exception\DomainException;
use Laminas\Hydrator\Strategy\Exception\InvalidArgumentException;
use ValueError;

use function assert;
use function get_debug_type;
use function is_a;
use function is_object;
use function is_scalar;
use function property_exists;
use function sprintf;

use const PHP_VERSION_ID;

final class BackedEnumStrategy implements StrategyInterface
{
    /** @var class-string  */
    private string $enumClass;

    /**
     * @param class-string $enumClass
     */
    public function __construct(string $enumClass)
    {
        if (PHP_VERSION_ID < 80100) {
            throw new DomainException("Backed enums require PHP 8.1+");
        }

        if (! is_a($enumClass, BackedEnum::class, true)) {
            throw new InvalidArgumentException("$enumClass is not a BackedEnum");
        }

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

        assert(is_object($value) && property_exists($value, 'value'));
        return $value->value;
    }

    /**
     * @inheritDoc
     */
    public function hydrate($value, ?array $data)
    {
        if ($value instanceof $this->enumClass) {
            return $value;
        }

        if (! is_scalar($value)) {
            throw new InvalidArgumentException(sprintf(
                "Value must be scalar; %s provided",
                get_debug_type($value)
            ));
        }

        try {
            /** @psalm-suppress MixedMethodCall */
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
