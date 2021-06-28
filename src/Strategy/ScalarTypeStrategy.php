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

    /** @var string */
    private $type;

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

    private function __construct(string $type)
    {
        $this->type = $type;
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

        switch ($this->type) {
            case self::TYPE_INT:
                return (int) $value;
            case self::TYPE_FLOAT:
                return (float) $value;
            case self::TYPE_STRING:
                return (string) $value;
            case self::TYPE_BOOLEAN:
                return (bool) $value;
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Unable to hydrate. Target type must be one of %s, %s was given.',
                        implode(', ', [self::TYPE_INT, self::TYPE_FLOAT, self::TYPE_STRING]),
                        $this->type
                    )
                );
        }
    }
}
