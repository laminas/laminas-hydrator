<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

interface HydratorProviderInterface
{
    /**
     * Provide plugin manager configuration for hydrators.
     *
     * @see    https://docs.mezzio.dev/mezzio/v3/features/container/config/#the-format
     *
     * @return mixed[][]
     */
    public function getHydratorConfig(): array;
}
