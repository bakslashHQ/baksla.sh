<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator
        ->add('app_home', '/')
        ->controller(TemplateController::class)
        ->defaults([
            'template' => 'home/index.html.twig',
        ]);

    $routingConfigurator
        ->add('app_legal_notices', '/legal-notices')
        ->controller(TemplateController::class)
        ->defaults([
            'template' => 'legal_notices/index.html.twig',
        ]);

    $routingConfigurator
        ->add('legacy_legal_notices', '/legal-notices.html')
        ->controller(RedirectController::class)
        ->defaults([
            'route' => 'app_legal_notices',
            'permanent' => true,
        ]);
};
