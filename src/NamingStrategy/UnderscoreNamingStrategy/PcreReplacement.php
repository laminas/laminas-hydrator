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

    public function __construct(public string $pattern, callable $replacement)
    {
        $this->replacement = $replacement;
    }
}
