<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Component\DomCrawler\Crawler;

final class ViewArticleTest extends FunctionalTestCase
{
    public function testRenderProperHtml(): void
    {
        $article = $this->getService(ArticleRepository::class)->get('symfony-certification');

        $this->get('/blog/symfony-certification');

        $this->assertSelectorTextContains('h1', $article->title);
        $this->assertSelectorTextContains('[data-test-author]', $article->author->getFullname());
        $this->assertSelectorExists('[data-test-article]');
        $this->assertSelectorExists('[data-test-more-articles]');
    }

    public function testThrowsNotFoundIfNoArticle(): void
    {
        $this->get('/blog/unexisting-article');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testMoreArticlesAreDisplayed(): void
    {
        $this->get('/blog/symfony-certification');

        $this->assertSelectorCount(3, '[data-test-more-articles] article');
    }

    public function testMoreArticlesDoNotContainCurrentArticle(): void
    {
        $article = $this->getService(ArticleRepository::class)->get('symfony-certification');

        $crawler = $this->get('/blog/symfony-certification');

        $titles = $crawler->filter('[data-test-more-articles] article h2')->each(static fn (Crawler $a): string => $a->text());

        $this->assertNotContains($article->title, $titles);
    }

    public function testReturnCachedVersionWhenPossible(): void
    {
        $articleRepository = $this->getService(ArticleRepository::class);
        $articlePreviewRepository = $this->getService(ArticlePreviewRepository::class);

        $this->get('/blog/symfony-certification');
        $this->assertResponseStatusCodeSame(200);

        $this->get('/blog/symfony-certification', server: [
            'HTTP_IF_NONE_MATCH' => sprintf('"%s%s"', $articleRepository->get('symfony-certification')->hash, $articlePreviewRepository->getHash()),
        ]);
        $this->assertResponseStatusCodeSame(304);
    }
}
