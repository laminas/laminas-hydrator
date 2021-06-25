<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols


declare(strict_types=1);

namespace Laminas\Hydrator;

use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;

trigger_error(sprintf(
    'Class %s is deprecated, please use %s instead',
    ArraySerializable::class,
    ArraySerializableHydrator::class
), E_USER_DEPRECATED);

/**
 * @deprecated since 3.0.0; to be removed in 4.0.0. Use ArraySerializableHydrator instead.
 */
class ArraySerializable extends ArraySerializableHydrator
{
}
