<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator
        ->import(sprintf('%s/src/Controller/', dirname(__DIR__)), 'attribute');

    $routingConfigurator
        ->add('legacy_legal_notices', '/legal-notices.html')
        ->controller(RedirectController::class)
        ->defaults([
            'route' => 'app_legal_notices',
            'permanent' => true,
        ]);
};
