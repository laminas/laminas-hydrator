<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

/**
 * @template T
 */
final class TypeCastStrategy implements StrategyInterface
{
    private const TYPE_INT = 'int';
    private const TYPE_FLOAT = 'float';
    private const TYPE_STRING = 'str';

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
        return call_user_func_array($this->type . 'val', [$value]);
    }
}
