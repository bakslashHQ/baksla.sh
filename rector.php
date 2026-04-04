<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassMethod\MakeInheritedMethodVisibilitySameAsParentRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withParallel()
    ->withCache(__DIR__ . '/var/rector')
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
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
        phpunitCodeQuality: true,
        symfonyCodeQuality: true,
    )
    ->withSkip([
        __DIR__ . '/config/bundles.php',
        __DIR__ . '/config/reference.php',
        MakeInheritedMethodVisibilitySameAsParentRector::class,
        NewlineAfterStatementRector::class,
    ])
;
