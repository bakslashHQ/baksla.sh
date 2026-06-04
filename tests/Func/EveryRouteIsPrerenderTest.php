<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Infrastructure\Controller\PreviewOpenGraphImage;
use App\Shared\Infrastructure\StaticSiteGeneration\Prerender;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;

final class EveryRouteIsPrerenderTest extends KernelTestCase
{
    public function testEveryRouteIsStaticallyGenerated(): void
    {
        self::bootKernel();

        $router = self::getContainer()->get(RouterInterface::class);

        $missing = [];

        foreach ($router->getRouteCollection() as $routeName => $route) {
            $controller = $route->getDefault('_controller');
            if (in_array($controller, [RedirectController::class, PreviewOpenGraphImage::class], true)) {
                continue;
            }

            if (!($route->getDefaults()[Prerender::ROUTE_DEFAULTS_KEY] ?? false)) {
                $missing[] = $routeName;
            }
        }

        $this->assertSame([], $missing, sprintf(
            "The following routes are not configured for static site generation:\n- %s",
            implode("\n- ", $missing),
        ));
    }
}
