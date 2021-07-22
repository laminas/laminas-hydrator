<?php

declare(strict_types=1);

namespace Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

use function array_key_exists;
use function mb_strtolower;
use function preg_replace;
use function preg_replace_callback;
use function strtolower;

/**
 * @internal
 */
final class CamelCaseToUnderscoreFilter
{
    use StringSupportTrait;

    /** @var string[] $transformedFilters */
    private $transformedFilters = [];

    public function filter(string $value): string
    {
        if (array_key_exists($value, $this->transformedFilters)) {
            return $this->transformedFilters[$value];
        }

        [$pattern, $replacement] = $this->getPatternAndReplacement();

        $filtered = preg_replace($pattern, $replacement, $value);

        $lowerFunction = $this->getLowerFunction();
        /** @var string $filteredValue */
        $filteredValue = $lowerFunction($filtered);

        $this->transformedFilters[$value] = $filteredValue;

        return $filteredValue;
    }

    /**
     * @return string[][] Array with two elements, first the patterns, then the
     *     replacements. Each element is an array of strings.
     */
    private function getPatternAndReplacement(): array
    {
        return $this->hasPcreUnicodeSupport()
            ? [
                [ // pattern
                    '#(?<=(?:\p{Lu}))(\p{Lu}\p{Ll})#',
                    '#(?<=(?:\p{Ll}|\p{Nd}))(\p{Lu})#',
                ],
                [ // replacement
                    '_\1',
                    '_\1',
                ],
            ]
            : [
                [ // pattern
                    '#(?<=(?:[A-Z]))([A-Z]+)([A-Z][a-z])#',
                    '#(?<=(?:[a-z0-9]))([A-Z])#',
                ],
                [ // replacement
                    '\1_\2',
                    '_\1',
                ],
            ];
    }

    private function getLowerFunction(): callable
    {
        return $this->hasMbStringSupport()
            ? function ($value) {
                return mb_strtolower($value, 'UTF-8');
            }
            : function ($value) {
                // ignore unicode characters w/ strtolower
                return preg_replace_callback('#([A-Z])#', function ($matches) {
                    return strtolower($matches[1]);
                }, $value);
            };
    }
}
