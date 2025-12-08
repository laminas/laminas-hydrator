<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

// Ignore unknown classes for PHP 8.1 compatibility. Remove after dropping PHP 8.1 support.
if (PHP_VERSION_ID < 80200) {
    $config->ignoreUnknownClasses([AllowDynamicProperties::class]);
}

return $config
    // intended soft dependency - Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy\CamelCaseToUnderscoreFilter
     ->ignoreErrorsOnExtension('ext-mbstring', [ErrorType::SHADOW_DEPENDENCY]);
