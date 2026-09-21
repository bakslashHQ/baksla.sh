<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Component\DomCrawler\Crawler;

final class ViewBlogTest extends FunctionalTestCase
{
    public function testRenderProperHtml(): void
    {
        $this->get('/blog');

        $this->assertSelectorTextContains('h1', 'blog.title');
        $this->assertSelectorExists('[data-test-article-link]');
    }

    public function testEveryArticleAreDisplayed(): void
    {
        $articleRepository = $this->getService(ArticleRepository::class);

        $this->get('/blog');

        $this->assertSelectorCount(count($articleRepository->findAll()), '[data-test-article-link]');
    }

    public function testLatestArticleIsDisplayedIfEnabled(): void
    {
        $articleRepository = $this->getService(ArticleRepository::class);

        $this->get('/blog');

        $latestSelector = '[data-test-article-link][data-latest]';
        if ($latest = $articleRepository->findLatest()) {
            $this->assertSelectorExists($latestSelector);
        } else {
            $this->assertSelectorNotExists($latestSelector);
        }
    }

    public function testNoArticleAreDuplicatedBecauseOfLatest(): void
    {
        $crawler = $this->get('/blog');

        $hrefs = $crawler->filter('[data-test-article-link]')->each(static fn (Crawler $a): string => (string) $a->attr('href'));

        $this->assertSameSize($hrefs, array_unique($hrefs));
    }
}
