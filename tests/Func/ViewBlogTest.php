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

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h2', 'blog.title');
    }

    public function testEveryArticleAreDisplayed(): void
    {
        $articlePreviewRepository = $this->getService(ArticlePreviewRepository::class);
        $articles = $articlePreviewRepository->findAll();

        $this->get('/blog');

        // Each article has an h2 inside a link
        $this->assertSelectorCount(count($articles), 'a h2');
    }

    public function testShowcasedArticleIsDisplayedIfEnabled(): void
    {
        $articlePreviewRepository = $this->getService(ArticlePreviewRepository::class);

        $this->get('/blog');

        // Showcased uses h2.font-bold (bigger/bolder than regular h2.font-semibold)
        if ($showcased = $articlePreviewRepository->findShowcased()) {
            $this->assertSelectorExists('h2.font-bold');
        } else {
            $this->assertSelectorNotExists('h2.font-bold');
        }
    }

    public function testNoArticleAreDuplicatedBecauseOfShowcased(): void
    {
        $crawler = $this->get('/blog');

        // Collect all article titles (both h2 and h3)
        $titles = $crawler->filter('h2 > a, h3 > a')->each(static fn (Crawler $a): string => $a->text());

        $this->assertSameSize($titles, array_unique($titles));
    }
}
