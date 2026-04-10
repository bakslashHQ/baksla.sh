<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Infrastructure\Controller\ViewBlog;
use App\Shared\Infrastructure\StaticSiteGeneration\Prerender;
use App\Team\Infrastructure\Controller\ViewTeam;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;

final class EveryRouteIsPrerenderTest extends KernelTestCase
{
    private const array REDIRECT_CONTROLLERS = [
        RedirectController::class,
        ViewBlog::class,
        ViewTeam::class,
    ];

    public function testEveryRouteIsStaticallyGenerated(): void
    {
        self::bootKernel();

        $router = self::getContainer()->get(RouterInterface::class);

        $missing = [];

        foreach ($router->getRouteCollection() as $routeName => $route) {
            $controller = $route->getDefault('_controller');

            if (\in_array($controller, self::REDIRECT_CONTROLLERS, true)) {
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
