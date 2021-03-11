<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use Laminas\Hydrator\Exception\InvalidArgumentException;

/**
 * @template T
 */
final class ScalarTypeStrategy implements StrategyInterface
{
    private const TYPE_INT = 'int';
    private const TYPE_FLOAT = 'float';
    private const TYPE_STRING = 'str';
    private const TYPE_BOOLEAN = 'boolean';

    /** @var string */
    private $type;

    /**
     * @return self<int>
     */
    public static function createToInt(): self
    {
        return new self(self::TYPE_INT);
    }

    /**
     * @return self<float>
     */
    public static function createToFloat(): self
    {
        return new self(self::TYPE_FLOAT);
    }

    /**
     * @return self<string>
     */
    public static function createToString(): self
    {
        return new self(self::TYPE_STRING);
    }

    /**
     * @return self<bool>
     */
    public static function createToBoolean(): self
    {
        return new self(self::TYPE_BOOLEAN);
    }

    /**
     * @param string $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $value
     * @param object|null $object
     * @return mixed
     */
    public function extract($value, ?object $object = null)
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @param array|null $data
     * @return T
     */
    public function hydrate($value, ?array $data)
    {
        if ($value === null) {
            return null;
        }

        switch ($this->type) {
            case self::TYPE_INT:
                return (int)$value;
            case self::TYPE_FLOAT:
                return (float)$value;
            case self::TYPE_STRING:
                return (string)$value;
            case self::TYPE_BOOLEAN:
                return (bool)$value;
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Unable to hydrate. Target type must be one of %s, %s was given.',
                        join(', ', [self::TYPE_INT, self::TYPE_FLOAT, self::TYPE_STRING]),
                        $this->type
                    )
                );
        }
    }
}
