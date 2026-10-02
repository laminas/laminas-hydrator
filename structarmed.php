<?php

declare(strict_types=1);

use Boundwize\StructArmed\Architecture;
use Boundwize\StructArmed\Preset\Preset;
use Boundwize\StructArmed\Rule\Rules\Class_\MustBeFinalRule;

return Architecture::define()
    ->withPresets(Preset::PSR4(), Preset::CODEQUALITY())
    ->layer('Exception', 'src/Exception')
    ->layer('Contract', [
        'src/ExtractionInterface.php',
        'src/HydrationInterface.php',
        'src/HydratorInterface.php',
    ])
    ->layer('Filter', 'src/Filter')
    ->layer('NamingStrategy', 'src/NamingStrategy')
    ->layer('StrategyException', 'src/Strategy/Exception')
    ->layer('Strategy', 'src/Strategy', 'src/Strategy/Exception')
    ->layer('Iterator', 'src/Iterator')
    ->layer('Aggregate', 'src/Aggregate')
    ->layer('Hydrator', 'src', [
        'src/Aggregate',
        'src/Exception',
        'src/ExtractionInterface.php',
        'src/Filter',
        'src/HydrationInterface.php',
        'src/HydratorInterface.php',
        'src/Iterator',
        'src/NamingStrategy',
        'src/Strategy',
    ])
    ->ruleset([
        'Exception'         => [],
        'Contract'          => [],
        'Filter'            => ['Exception'],
        'NamingStrategy'    => ['Exception'],
        'StrategyException' => [],
        'Strategy'          => ['Contract', 'Exception', 'StrategyException'],
        'Iterator'          => ['Contract', 'Exception'],
        'Aggregate'         => ['Contract'],
        'Hydrator'          => ['+Filter', '+NamingStrategy', '+Strategy'],
    ])

    ->layer('tests', 'test')
    ->rule(
        'tests_classes.must_be_final',
        new MustBeFinalRule(layer: 'tests')
    );
