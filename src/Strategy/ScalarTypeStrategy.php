<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Hydrator\Exception\InvalidArgumentException;

use function implode;
use function sprintf;

final class ScalarTypeStrategy implements StrategyInterface
{
    private const TYPE_INT     = 'int';
    private const TYPE_FLOAT   = 'float';
    private const TYPE_STRING  = 'string';
    private const TYPE_BOOLEAN = 'bool';

    public static function createToInt(): self
    {
        return new self(self::TYPE_INT);
    }

    public static function createToFloat(): self
    {
        return new self(self::TYPE_FLOAT);
    }

    public static function createToString(): self
    {
        return new self(self::TYPE_STRING);
    }

    public static function createToBoolean(): self
    {
        return new self(self::TYPE_BOOLEAN);
    }

    private function __construct(private string $type)
    {
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function extract($value, ?object $object = null)
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @param array|null $data
     * @psalm-return null|scalar
     */
    public function hydrate($value, ?array $data)
    {
        if ($value === null) {
            return null;
        }

        return match ($this->type) {
            self::TYPE_INT => (int) $value,
            self::TYPE_FLOAT => (float) $value,
            self::TYPE_STRING => (string) $value,
            self::TYPE_BOOLEAN => (bool) $value,
            default => throw new InvalidArgumentException(
                sprintf(
                    'Unable to hydrate. Target type must be one of %s, %s was given.',
                    implode(', ', [self::TYPE_INT, self::TYPE_FLOAT, self::TYPE_STRING]),
                    $this->type
                )
            ),
        };
    }
}
