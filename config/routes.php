<?php

declare(strict_types=1);

use App\Shared\Infrastructure\StaticSiteGeneration\Prerender;
use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator
        ->add('app_home', [
            'en' => '/',
            'fr' => '/fr',
        ])
        ->controller(TemplateController::class)
        ->defaults([
            'template' => 'pages/website/home.html.twig',
            Prerender::ROUTE_DEFAULTS_KEY => true,
        ]);

    $routingConfigurator
        ->add('app_legal_notices', [
            'en' => '/legal-notices',
            'fr' => '/fr/mentions-legales',
        ])
        ->controller(TemplateController::class)
        ->defaults([
            'template' => 'pages/website/legal_notices.html.twig',
            Prerender::ROUTE_DEFAULTS_KEY => true,
        ]);

    $routingConfigurator
        ->add('app_robots', '/robots.txt')
        ->controller(TemplateController::class)
        ->format('txt')
        ->defaults([
            'template' => 'pages/website/robots.txt.twig',
            Prerender::ROUTE_DEFAULTS_KEY => true,
        ]);

    $routingConfigurator
        ->import(sprintf('%s/src/Website/Infrastructure/Controller/', dirname(__DIR__)), 'attribute');

    $routingConfigurator
        ->import(sprintf('%s/src/Blog/Infrastructure/Controller/', dirname(__DIR__)), 'attribute');

    $routingConfigurator
        ->import(sprintf('%s/src/Team/Infrastructure/Controller/', dirname(__DIR__)), 'attribute');
};
