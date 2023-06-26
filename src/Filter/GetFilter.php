<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Filter;

use function strpos;

final class GetFilter implements FilterInterface
{
    public function filter(string $property, ?object $instance = null): bool
    {
        $pos = strpos($property, '::');
        if ($pos !== false) {
            $pos += 2;
        } else {
            $pos = 0;
        }

        return strpos($property, 'get', $pos) === $pos;
    }
}
