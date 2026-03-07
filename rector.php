<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\CodeQuality\Rector\Class_\YieldDataProviderRector;
use Rector\PHPUnit\CodeQuality\Rector\MethodCall\AssertEqualsOrAssertSameFloatParameterToSpecificMethodsTypeRector;

return RectorConfig::configure()
    ->withPhpSets(php81: true)
    ->withAttributesSets()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/test',
    ])
    ->withPreparedSets(
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        phpunitCodeQuality: true,
    )
    ->withSkipPath(__DIR__ . '/test/TestAsset')
    ->withSkip([
        YieldDataProviderRector::class,
        AssertEqualsOrAssertSameFloatParameterToSpecificMethodsTypeRector::class,
    ]);
