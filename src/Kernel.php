<?php

declare(strict_types=1);

namespace App;

use App\Shared\Infrastructure\StaticSiteGeneration\ParamsProviderInterface;
use App\Shared\Infrastructure\StaticSiteGeneration\StaticSiteGenerationPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(ParamsProviderInterface::class)
            ->addTag('ssg.params_provider');

        $container->addCompilerPass(new StaticSiteGenerationPass());
    }
}
