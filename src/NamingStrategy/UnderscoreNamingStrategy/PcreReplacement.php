<?php

declare(strict_types=1);

namespace Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

/**
 * Describe a PCRE pattern and a callback for providing a replacement.
 *
 * @internal
 */
class PcreReplacement
{
    /** @var callable */
    public $replacement;
    /** @var non-empty-string */
    public string $pattern;

    /**
     * @param non-empty-string $pattern
     */
    public function __construct(string $pattern, callable $replacement)
    {
        $this->replacement = $replacement;
        $this->pattern     = $pattern;
    }
}
