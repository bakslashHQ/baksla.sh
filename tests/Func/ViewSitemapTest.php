<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Website\Infrastructure\Controller\ViewSitemap;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class ViewSitemapTest extends FunctionalTestCase
{
    public function testRenderProperXml(): void
    {
        $articlePreviewRepository = $this->getService(ArticlePreviewRepository::class);

        $this->get('/sitemap.xml');

        $response = $this->getResponse();

        $this->assertStringContainsString('text/xml', $response->headers->get('Content-Type') ?? '');

        $xml = simplexml_load_string($response->getContent() ?: '');

        /** @var array{url: list<array{loc: string, lastmod?: string}>} $xml */
        $xml = json_decode(json_encode($xml) ?: '', associative: true);

        $urls = array_map(static fn (array $u): string => $u['loc'], $xml['url']);

        $this->assertContains('https://localhost/', $urls);
        $this->assertContains('https://localhost/blog', $urls);
        $this->assertContains('https://localhost/team', $urls);
        $this->assertContains('https://localhost/legal-notices', $urls);

        $articleUrls = array_map(static fn (ArticlePreview $a): string => sprintf('https://localhost/blog/%s', $a->id), $articlePreviewRepository->findAll());
        foreach ($articleUrls as $articleUrl) {
            $this->assertContains($articleUrl, $urls);
        }
    }

    public function testSitemapCoversEveryPageRoute(): void
    {
        $router = $this->getService(RouterInterface::class);

        $this->get('/sitemap.xml');

        $xml = simplexml_load_string($this->getResponse()->getContent() ?: '');

        /** @var array{url: list<array{loc: string}>} $xml */
        $xml = json_decode(json_encode($xml) ?: '', associative: true);

        $sitemapUrls = array_map(static fn (array $u): string => $u['loc'], $xml['url']);

        $expectedUrls = [];
        foreach ($router->getRouteCollection() as $name => $route) {
            if (!$this->isPageRoute($name, $route)) {
                continue;
            }

            foreach ($this->provideExpectedParams($name, $route) as $params) {
                $expectedUrls[] = $router->generate($name, $params, UrlGeneratorInterface::ABSOLUTE_URL);
            }
        }

        $missing = array_values(array_diff($expectedUrls, $sitemapUrls));

        $this->assertSame([], $missing, sprintf(
            "Sitemap is missing %d page URL(s):\n  - %s\nAdd them in %s.",
            \count($missing),
            implode("\n  - ", $missing),
            ViewSitemap::class,
        ));
    }

    public function testSitemapHasNoExtraEntries(): void
    {
        $router = $this->getService(RouterInterface::class);

        $this->get('/sitemap.xml');

        $xml = simplexml_load_string($this->getResponse()->getContent() ?: '');

        /** @var array{url: list<array{loc: string}>} $xml */
        $xml = json_decode(json_encode($xml) ?: '', associative: true);

        $sitemapUrls = array_map(static fn (array $u): string => $u['loc'], $xml['url']);

        $extras = [];
        foreach ($sitemapUrls as $url) {
            $path = parse_url($url, \PHP_URL_PATH);
            Assert::string($path);

            try {
                $params = $router->match($path);
            } catch (ResourceNotFoundException) {
                $extras[] = $url;

                continue;
            }

            $name = $params['_route'];
            Assert::string($name);

            $route = $router->getRouteCollection()->get($name);
            Assert::notNull($route);

            if (!$this->isPageRoute($name, $route)) {
                $extras[] = $url;
            }
        }

        $this->assertSame([], $extras, sprintf(
            "Sitemap contains %d entries that do not resolve to a page route:\n  - %s\nRemove them from %s.",
            \count($extras),
            implode("\n  - ", $extras),
            ViewSitemap::class,
        ));
    }

    public function testArticleEntriesExposeLastmod(): void
    {
        $articlePreviewRepository = $this->getService(ArticlePreviewRepository::class);

        $this->get('/sitemap.xml');

        $xml = simplexml_load_string($this->getResponse()->getContent() ?: '');

        /** @var array{url: list<array{loc: string, lastmod?: string}>} $xml */
        $xml = json_decode(json_encode($xml) ?: '', associative: true);

        $byLoc = [];
        foreach ($xml['url'] as $u) {
            $byLoc[$u['loc']] = $u;
        }

        foreach ($articlePreviewRepository->findAll() as $article) {
            $loc = sprintf('https://localhost/blog/%s', $article->id);

            $this->assertArrayHasKey('lastmod', $byLoc[$loc], sprintf('Article %s should expose <lastmod>', $article->id));
            $this->assertSame($article->publishedAt->format('Y-m-d'), $byLoc[$loc]['lastmod'] ?? null);
        }
    }

    /**
     * @return iterable<array<string, mixed>>
     */
    private function provideExpectedParams(string $name, Route $route): iterable
    {
        if ($route->compile()->getVariables() === []) {
            yield [];

            return;
        }

        if ($name === 'app_blog_article') {
            foreach ($this->getService(ArticlePreviewRepository::class)->findAll() as $article) {
                yield [
                    'id' => $article->id,
                ];
            }

            return;
        }

        self::fail(sprintf('No params provider for parametric route "%s". Update %s::provideExpectedParams().', $name, self::class));
    }

    private function isPageRoute(string $name, Route $route): bool
    {
        if ($name === 'app_sitemap') {
            return false;
        }

        $methods = $route->getMethods();
        if ($methods !== [] && !\in_array('GET', $methods, true)) {
            return false;
        }

        $format = $route->getDefault('_format');
        if ($format !== null && $format !== 'html') {
            return false;
        }

        $controller = $route->getDefault('_controller');
        return !(\is_string($controller) && str_starts_with($controller, RedirectController::class));
    }
}
