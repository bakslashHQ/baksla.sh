<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withParallel()
    ->withCache(__DIR__ . '/var/ecs')
    ->withPaths([
        __DIR__ . '/assets',
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/tools',
    ])
    ->withSkip([
        __DIR__ . '/config/bundles.php',
    ])
    ->withPreparedSets(
        psr12: true,
        common: true,
        strict: true,
        cleanCode: true,
    )
;
