<?php

declare(strict_types=1);

namespace Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

/**
 * Describe a PCRE pattern and a callback for providing a replacement.
 *
 * @internal
 */
final class PcreReplacement
{
    /** @var callable */
    public $replacement;

    /**
     * @param non-empty-string $pattern
     */
    public function __construct(public string $pattern, callable $replacement)
    {
        $this->replacement = $replacement;
    }
}
