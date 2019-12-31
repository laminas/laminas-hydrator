<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Hydrator\Strategy;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

final class DateTimeFormatterStrategy implements StrategyInterface
{
    /**
     * Format to use during hydration.
     *
     * @var string
     */
    private $format;

    /**
     * @var DateTimeZone|null
     */
    private $timezone;

    /**
     * Format to use during extraction.
     *
     * Removes any special anchor characters used to ensure that creation of a
     * `DateTime` instance uses the formatted time string (which is useful
     * during hydration).  These include `!` at the beginning of the string and
     * `|` at the end.
     *
     * @var string
     */
    private $extractionFormat;

    /**
     * Constructor
     *
     * @param string            $format
     * @param DateTimeZone|null $timezone
     */
    public function __construct($format = DateTime::RFC3339, DateTimeZone $timezone = null)
    {
        $this->format = (string) $format;
        $this->timezone = $timezone;
        $this->extractionFormat = preg_replace('/(?<![\\\\])[+|!\*]/', '', $this->format);
    }

    /**
     * {@inheritDoc}
     *
     * Converts to date time string
     *
     * @param mixed|DateTimeInterface $value
     * @return mixed|string
     */
    public function extract($value)
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format($this->extractionFormat);
        }

        return $value;
    }

    /**
     * Converts date time string to DateTime instance for injecting to object
     *
     * {@inheritDoc}
     *
     * @param mixed|string $value
     * @return mixed|DateTime
     */
    public function hydrate($value)
    {
        if ($value === '' || $value === null) {
            return;
        }

        $hydrated = $this->timezone
            ? DateTime::createFromFormat($this->format, $value, $this->timezone)
            : DateTime::createFromFormat($this->format, $value);

        return $hydrated ?: $value;
    }
}
