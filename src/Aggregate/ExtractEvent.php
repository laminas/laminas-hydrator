<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Aggregate;

use Laminas\EventManager\Event;

use function array_merge;

/**
 * Event triggered when the {@see AggregateHydrator} extracts
 * data from an object
 *
 * @template TTarget of object
 * @extends Event<TTarget, array<empty, empty>>
 * @final
 */
class ExtractEvent extends Event
{
    public const EVENT_EXTRACT = 'extract';

    /**
     * {@inheritDoc}
     */
    protected $name = self::EVENT_EXTRACT;

    /** @var mixed[] Data being extracted from the $extractionObject */
    protected $extractedData = [];

    /** @psalm-param TTarget $target */
    public function __construct(object $target, protected object $extractionObject)
    {
        parent::__construct(self::EVENT_EXTRACT, $target, []);
    }

    /**
     * Retrieves the object from which data is extracted
     */
    public function getExtractionObject(): object
    {
        return $this->extractionObject;
    }

    public function setExtractionObject(object $extractionObject): void
    {
        $this->extractionObject = $extractionObject;
    }

    /**
     * Retrieves the data that has been extracted
     *
     * @return mixed[]
     */
    public function getExtractedData(): array
    {
        return $this->extractedData;
    }

    /**
     * @param mixed[] $extractedData
     */
    public function setExtractedData(array $extractedData): void
    {
        $this->extractedData = $extractedData;
    }

    /**
     * Merge provided data with the extracted data
     *
     * @param mixed[] $additionalData
     */
    public function mergeExtractedData(array $additionalData): void
    {
        $this->extractedData = array_merge($this->extractedData, $additionalData);
    }
}
