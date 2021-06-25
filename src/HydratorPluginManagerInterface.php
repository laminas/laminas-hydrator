<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

use Psr\Container\ContainerInterface;

/**
 * @method HydratorInterface get(string $id)
 */
interface HydratorPluginManagerInterface extends ContainerInterface
{
}
