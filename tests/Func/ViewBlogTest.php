<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Repository\ArticlePreviewRepository;
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
        $articlePreviewRepository = $this->getService(ArticlePreviewRepository::class);

        $this->get('/blog');

        $this->assertSelectorCount(count($articlePreviewRepository->findAll()), '[data-test-article-link]');
    }

    public function testShowcasedArticleIsDisplayedIfEnabled(): void
    {
        $articlePreviewRepository = $this->getService(ArticlePreviewRepository::class);

        $this->get('/blog');

        $showcasedSelector = '[data-test-article-link][data-showcased]';
        if ($showcased = $articlePreviewRepository->findShowcased()) {
            $this->assertSelectorExists($showcasedSelector);
        } else {
            $this->assertSelectorNotExists($showcasedSelector);
        }
    }

    public function testNoArticleAreDuplicatedBecauseOfShowcased(): void
    {
        $crawler = $this->get('/blog');

        $hrefs = $crawler->filter('[data-test-article-link]')->each(static fn (Crawler $a): string => (string) $a->attr('href'));

        $this->assertSameSize($hrefs, array_unique($hrefs));
    }
}
