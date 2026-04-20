<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    // intended soft dependency - Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy\CamelCaseToUnderscoreFilter
     ->ignoreErrorsOnExtension('ext-mbstring', [ErrorType::SHADOW_DEPENDENCY]);
