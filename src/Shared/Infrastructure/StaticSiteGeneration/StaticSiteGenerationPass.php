<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\StaticSiteGeneration;

use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class StaticSiteGenerationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition(PrerenderRouteLoader::class)
            ->setDecoratedService('routing.loader')
            ->setArgument('$inner', new Reference('.inner'));

        $container->getDefinition(StaticPageUrisProvider::class)
            ->setArgument('$paramsProviders', new ServiceLocatorArgument(new TaggedIteratorArgument('ssg.params_provider', indexAttribute: 'key')));

        $container->getDefinition(StaticPagesGenerator::class)
            ->setArgument('$kernel', new Reference('http_kernel'));

        $container->getDefinition(FilesystemStaticPageDumper::class)
            ->setArgument('$outputDir', '%app.ssg_output_dir%');
    }
}
