<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\FunctionLike\ReturnTypeDeclarationRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/test'
    ]);

    require_once 'vendor/rector/rector/rules/CodingStyle/Rector/ArrowFunction/StaticArrowFunctionRector.php';
    // register a single rule
    $rectorConfig->rule(ReturnTypeDeclarationRector::class);
};
