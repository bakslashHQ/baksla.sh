<?php

declare(strict_types=1);

namespace App\Tests\Func;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;

final class EveryRouteIsLocalizedTest extends KernelTestCase
{
    /**
     * Routes intentionally served from a single URL (root machine-facing files,
     * internal previews) and therefore not localized.
     *
     * @var list<string>
     */
    private const array NON_LOCALIZED_ROUTES = [
        'app_robots',
        'app_sitemap',
        'app_llms',
    ];

    public function testEveryRouteIsLocalized(): void
    {
        self::bootKernel();

        $router = self::getContainer()->get(RouterInterface::class);

        $notLocalized = [];

        foreach ($router->getRouteCollection() as $routeName => $route) {
            if (in_array($routeName, self::NON_LOCALIZED_ROUTES, true)) {
                continue;
            }

            if ($route->getDefault('_locale') === null) {
                $notLocalized[] = $routeName;
            }
        }

        $this->assertSame([], $notLocalized, sprintf(
            "The following routes are not localized. Localize them, or add them to %s::NON_LOCALIZED_ROUTES if intentional:\n- %s",
            self::class,
            implode("\n- ", $notLocalized),
        ));
    }
}
