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
        $this->assertSelectorExists('[data-test-articles] article');
    }

    public function testEveryArticleAreDisplayed(): void
    {
        $articlePreviewRepository = $this->getService(ArticlePreviewRepository::class);

        $this->get('/blog');

        $this->assertSelectorCount(count($articlePreviewRepository->findAll()), '[data-test-articles] article a');
    }

    public function testShowcasedArticleIsDisplayedIfEnabled(): void
    {
        $articlePreviewRepository = $this->getService(ArticlePreviewRepository::class);

        $this->get('/blog');

        $showcasedSelector = '[data-test-articles] article[showcased]';
        if ($showcased = $articlePreviewRepository->findShowcased()) {
            $this->assertSelectorExists($showcasedSelector);
        } else {
            $this->assertSelectorNotExists($showcasedSelector);
        }
    }

    public function testNoArticleAreDuplicatedBecauseOfShowcased(): void
    {
        $crawler = $this->get('/blog');

        $titles = $crawler->filter('[data-test-articles] article h2')->each(static fn (Crawler $a): string => $a->text());

        $this->assertSameSize($titles, array_unique($titles));
    }

    public function testReturnCachedVersionWhenPossible(): void
    {
        $articlePreviewRepository = $this->getService(ArticlePreviewRepository::class);

        $this->get('/blog');
        $this->assertResponseStatusCodeSame(200);

        $this->get('/blog', server: [
            'HTTP_IF_NONE_MATCH' => sprintf('"%s"', $articlePreviewRepository->getHash()),
        ]);
        $this->assertResponseStatusCodeSame(304);
    }
}
