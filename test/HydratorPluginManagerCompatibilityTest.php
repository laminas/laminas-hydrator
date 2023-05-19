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
class HydratorPluginManagerCompatibilityTest extends TestCase
{
    use CommonPluginManagerTrait;

    /** @return HydratorPluginManager */
    protected static function getPluginManager()
    {
        return new HydratorPluginManager(new ServiceManager());
    }

    /**
     * @return void
     */
    protected function getV2InvalidPluginException()
    {
        // no-op
    }

    /**
     * @return string
     * @psalm-return class-string
     */
    protected function getInstanceOf()
    {
        return HydratorInterface::class;
    }
}
