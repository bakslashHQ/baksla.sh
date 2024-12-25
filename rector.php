<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withParallel()
    ->withCache(__DIR__ . '/var/rector')
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
        phpunitCodeQuality: true,
        doctrineCodeQuality: true,
        symfonyCodeQuality: true,
    );
