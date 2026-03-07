<?php

declare(strict_types=1);

namespace Laminas\Hydrator\Exception;

use Psr\Container\NotFoundExceptionInterface;

use function sprintf;

final class MissingHydratorServiceException extends InvalidArgumentException implements NotFoundExceptionInterface
{
    public static function forService(string $serviceName): self
    {
        return new self(sprintf(
            'Unable to resolve "%s" to a hydrator service.',
            $serviceName
        ));
    }
}
