<?php

declare(strict_types=1);

use App\Team\Infrastructure\Repository\InMemoryMemberRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $params = $containerConfigurator->parameters();

    $params
        ->set('app.public_dir', sprintf('%s/public', param('kernel.project_dir')))
        ->set('app.showcased_article', 'webpack-encore-whats-new-8-months-later') // The filename without the ".md.twig" extension
        ->set('app.articles_dir', sprintf('%s/templates/articles', param('kernel.project_dir')))
        ->set('app.ssg_output_dir', sprintf('%s/static-pages', param('app.public_dir')))
        ->set('app.open_source_stats_file', $containerConfigurator->env() === 'test'
            ? sprintf('%s/bakslash_test/open-source-stats.json', sys_get_temp_dir())
            : sprintf('%s/data/open-source-stats.json', param('kernel.project_dir')));

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\', sprintf('%s/src/', dirname(__DIR__)))
        ->exclude([
            sprintf('%s/src/DependencyInjection/', dirname(__DIR__)),
            sprintf('%s/src/Kernel.php', dirname(__DIR__)),
        ]);

    $services
        ->get(InMemoryMemberRepository::class)
        ->factory([InMemoryMemberRepository::class, 'createDefault']);
};
