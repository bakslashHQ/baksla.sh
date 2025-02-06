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
            'template' => 'pages/website/home.html.twig',
        ]);

    $routingConfigurator
        ->add('app_legal_notices', '/legal-notices')
        ->controller(TemplateController::class)
        ->defaults([
            'template' => 'pages/website/legal_notices.html.twig',
        ]);

    $routingConfigurator
        ->add('legacy_legal_notices', '/legal-notices.html')
        ->controller(RedirectController::class)
        ->defaults([
            'route' => 'app_legal_notices',
            'permanent' => true,
        ]);

    $routingConfigurator
        ->add('app_robots', '/robots.txt')
        ->controller(TemplateController::class)
        ->format('txt')
        ->defaults([
            'template' => 'pages/website/robots.txt.twig',
        ]);

    $routingConfigurator
        ->import(sprintf('%s/src/Website/Controller/', dirname(__DIR__)), 'attribute');

    $routingConfigurator
        ->import(sprintf('%s/src/Blog/Infrastructure/Symfony/Controller/', dirname(__DIR__)), 'attribute');
};
