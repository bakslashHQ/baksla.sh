<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\', sprintf('%s/src/', dirname(__DIR__)))
        ->exclude([
            sprintf('%s/src/DependencyInjection/', dirname(__DIR__)),
            sprintf('%s/src/Kernel.php', dirname(__DIR__)),
        ]);
};
