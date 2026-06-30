<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\LocaleSwitcher;

final class ViewArticleTest extends FunctionalTestCase
{
    public function testRenderProperHtml(): void
    {
        $article = $this->getService(ArticleRepository::class)->get('symfony-certification');

        $this->get('/blog/symfony-certification');

        $this->assertSelectorTextContains('h1', $article->title);
        $this->assertSelectorTextContains('[data-test-author]', $article->author->getFullname());
        $this->assertSelectorExists('[data-test-article]');
    }

    public function testHeaderExposesPublishedAt(): void
    {
        $article = $this->getService(ArticleRepository::class)->get('symfony-certification');

        $crawler = $this->get('/blog/symfony-certification');

        $this->assertSame($article->publishedAt->format('Y-m-d'), $crawler->filter('article header time')->attr('datetime'));
    }

    public function testFrenchMarkdownAlternateReturnsFrenchSource(): void
    {
        $this->get('/fr/blog/certification-symfony.md');

        $response = $this->getResponse();

        $this->assertResponseIsSuccessful();
        $body = $response->getContent() ?: '';
        $this->assertStringContainsString('title: La certification Symfony', $body);
    }

    public function testExposesHreflangAlternatesWithTranslatedSlugs(): void
    {
        $crawler = $this->get('/fr/blog/certification-symfony');

        $this->assertResponseIsSuccessful();

        $alternates = [];
        foreach ($crawler->filter('link[rel="alternate"][hreflang]')->each(static fn (Crawler $node): array => [$node->attr('hreflang'), $node->attr('href')]) as [$hreflang, $href]) {
            $alternates[$hreflang] = $href;
        }

        $this->assertSame('https://localhost/blog/symfony-certification', $alternates['en'] ?? null);
        $this->assertSame('https://localhost/fr/blog/certification-symfony', $alternates['fr'] ?? null);
        $this->assertSame('https://localhost/blog/symfony-certification', $alternates['x-default'] ?? null);
    }

    public function testArticleBodyInternalLinksResolveInEveryLocale(): void
    {
        $articleRepository = $this->getService(ArticleRepository::class);
        $localeSwitcher = $this->getService(LocaleSwitcher::class);
        $urlGenerator = $this->getService(UrlGeneratorInterface::class);

        /** @var list<string> $locales */
        $locales = self::getContainer()->getParameter('kernel.enabled_locales');

        foreach ($locales as $locale) {
            /** @var list<Article> $articles */
            $articles = $localeSwitcher->runWithLocale($locale, fn (): array => $articleRepository->findAll());

            foreach ($articles as $article) {
                $path = $urlGenerator->generate('app_blog_article', [
                    'slug' => $article->slug,
                    '_locale' => $locale,
                ]);

                $crawler = $this->get($path);
                $this->assertResponseIsSuccessful();

                $hrefs = $crawler->filter('[data-test-article] a[href]')->each(static fn (Crawler $a): string => (string) $a->attr('href'));

                foreach ($hrefs as $href) {
                    if (str_starts_with($href, 'http')) {
                        continue;
                    }
                    if (str_starts_with($href, '#')) {
                        continue;
                    }
                    if (str_starts_with($href, 'mailto:')) {
                        continue;
                    }
                    $target = str_starts_with($href, '/') ? $href : sprintf('%s/%s', \dirname($path), $href);

                    $this->get($target);
                    $this->assertResponseIsSuccessful(sprintf('Internal link "%s" in article "%s" (%s) must resolve.', $href, $article->slug, $locale));
                }
            }
        }
    }

    public function testCodeBlockIsKeyboardAccessible(): void
    {
        $crawler = $this->get('/blog/webpack-encore-whats-new-8-months-later');

        $pre = $crawler->filter('pre[data-lang]')->first();

        $this->assertSame('0', $pre->attr('tabindex'));
        $this->assertNotEmpty($pre->attr('aria-label'));
    }

    public function testThrowsNotFoundIfNoArticle(): void
    {
        $this->get('/blog/unexisting-article');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testMarkdownAlternateLinkIsExposed(): void
    {
        $crawler = $this->get('/blog/symfony-certification');

        $href = $crawler->filter('link[rel="alternate"][type="text/markdown"]')->attr('href');

        $this->assertSame('https://localhost/blog/symfony-certification.md', $href);
    }

    public function testMarkdownAlternateReturnsMarkdownSource(): void
    {
        $this->get('/blog/symfony-certification.md');

        $response = $this->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('text/markdown', $response->headers->get('Content-Type') ?? '');

        $body = $response->getContent() ?: '';
        $this->assertStringContainsString('title: Symfony Certification', $body);
        $this->assertStringContainsString('author: mathias-arlaud', $body);
    }

    public function testMarkdownAlternateThrowsNotFoundIfNoArticle(): void
    {
        $this->get('/blog/unexisting-article.md');

        $this->assertResponseStatusCodeSame(404);
    }
}
