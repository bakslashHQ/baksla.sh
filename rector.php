<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ClassMethod\MakeInheritedMethodVisibilitySameAsParentRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;

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
        ReadOnlyClassRector::class => [
            __DIR__ . '/src/*/Infrastructure/Rendering/Components/*.php',
        ],
        ReadOnlyPropertyRector::class => [
            __DIR__ . '/src/*/Infrastructure/Rendering/Components/*.php',
        ],
    ])
;
