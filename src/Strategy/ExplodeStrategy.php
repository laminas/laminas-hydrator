<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Strategy;

use function explode;
use function gettype;
use function implode;
use function is_array;
use function is_numeric;
use function is_object;
use function is_string;
use function sprintf;

final class ExplodeStrategy implements StrategyInterface
{
    /** @var non-empty-string */
    private string $valueDelimiter;

    /**
     * Constructor
     *
     * @param non-empty-string $delimiter String that the values will be split upon
     * @param int|null $explodeLimit Explode limit
     */
    public function __construct(string $delimiter = ',', private ?int $explodeLimit = null)
    {
        $this->setValueDelimiter($delimiter);
    }

    /**
     * Sets the delimiter string that the values will be split upon
     */
    private function setValueDelimiter(string $delimiter): void
    {
        if (empty($delimiter)) {
            throw new Exception\InvalidArgumentException('Delimiter cannot be empty.');
        }

        $this->valueDelimiter = $delimiter;
    }

    /**
     * {@inheritDoc}
     *
     * Split a string by delimiter
     *
     * @param string|null $value
     * @return string[]
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate($value, ?array $data = null)
    {
        if (null === $value) {
            return [];
        }

        if (! (is_string($value) || is_numeric($value))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects argument 1 to be string, %s provided instead',
                __METHOD__,
                is_object($value) ? $value::class : gettype($value)
            ));
        }

        if ($this->explodeLimit !== null) {
            return explode($this->valueDelimiter, (string) $value, $this->explodeLimit);
        }

        return explode($this->valueDelimiter, (string) $value);
    }

    /**
     * {@inheritDoc}
     *
     * Join array elements with delimiter
     *
     * @param string[] $value The original value.
     * @return string|null
     * @throws Exception\InvalidArgumentException For non-array $value values.
     */
    public function extract($value, ?object $object = null)
    {
        if (! is_array($value)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects argument 1 to be array, %s provided instead',
                __METHOD__,
                is_object($value) ? $value::class : gettype($value)
            ));
        }

        return empty($value) ? null : implode($this->valueDelimiter, $value);
    }
}
