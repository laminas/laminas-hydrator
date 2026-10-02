<?php

declare(strict_types=1);

namespace Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

use Laminas\Stdlib\StringUtils;

use function extension_loaded;

/**
 * @internal
 */
trait StringSupportTrait
{
    private ?bool $pcreUnicodeSupport = null;

    private ?bool $mbStringSupport = null;

    private function hasPcreUnicodeSupport(): bool
    {
        $this->pcreUnicodeSupport ??= StringUtils::hasPcreUnicodeSupport();
        return $this->pcreUnicodeSupport;
    }

    private function hasMbStringSupport(): bool
    {
        $this->mbStringSupport ??= extension_loaded('mbstring');
        return $this->mbStringSupport;
    }
}
