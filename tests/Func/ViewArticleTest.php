<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Repository\ArticleRepository;

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
}
