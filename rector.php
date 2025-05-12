<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassMethod\MakeInheritedMethodVisibilitySameAsParentRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;

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
        symfonyCodeQuality: true,
    )
    ->withSkip([
        MakeInheritedMethodVisibilitySameAsParentRector::class,
        FirstClassCallableRector::class => [
            // e.g.: when calling ->factory() with a callable, we want to keep the array notation in place
            __DIR__ . '/config/services.php',
        ],
        NewlineAfterStatementRector::class,
    ])
;
