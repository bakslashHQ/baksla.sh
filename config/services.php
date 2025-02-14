<?php

declare(strict_types=1);

use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Blog\Domain\Repository\ArticleRepository;
use App\Blog\Infrastructure\Cache\CacheArticlePreviewRepository;
use App\Blog\Infrastructure\Cache\CacheArticleRepository;
use App\Blog\Infrastructure\Filesystem\FilesystemArticlePreviewRepository;
use App\Blog\Infrastructure\Filesystem\FilesystemArticleRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\inline_service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $params = $containerConfigurator->parameters();

    $params
        ->set('app.showcased_article', 'symfony-certification') // The filename without the ".md.twig" extension
        ->set('app.articles_dir', sprintf('%s/templates/articles', param('kernel.project_dir')));

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\', sprintf('%s/src/', dirname(__DIR__)))
        ->exclude([
            sprintf('%s/src/DependencyInjection/', dirname(__DIR__)),
            sprintf('%s/src/Kernel.php', dirname(__DIR__)),
        ]);

    $services->stack('article_repository', [
        inline_service(CacheArticleRepository::class)
            ->args([
                service('article_cache'),
                service('.inner'),
            ]),
        inline_service(FilesystemArticleRepository::class),
    ]);
    $services->alias(ArticleRepository::class, 'article_repository');

    $services->stack('article_preview_repository', [
        inline_service(CacheArticlePreviewRepository::class)
            ->args([
                service('article_preview_cache'),
                service('.inner'),
            ]),
        inline_service(FilesystemArticlePreviewRepository::class),
    ]);
    $services->alias(ArticlePreviewRepository::class, 'article_preview_repository');
};
