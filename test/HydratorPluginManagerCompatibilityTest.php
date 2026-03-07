<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\ServiceManager\Test\CommonPluginManagerTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HydratorPluginManager::class)]
final class HydratorPluginManagerCompatibilityTest extends TestCase
{
    use CommonPluginManagerTrait;

    protected static function getPluginManager(array $config = []): HydratorPluginManager
    {
        return new HydratorPluginManager(new ServiceManager());
    }

    /**
     * @psalm-return class-string
     */
    protected function getInstanceOf(): string
    {
        return HydratorInterface::class;
    }
}
