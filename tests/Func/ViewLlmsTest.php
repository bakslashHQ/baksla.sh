<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Repository\ArticleRepository;

final class ViewLlmsTest extends FunctionalTestCase
{
    public function testServesAsMarkdown(): void
    {
        $this->get('/llms.txt');

        $contentType = $this->getResponse()->headers->get('Content-Type') ?? '';
        $this->assertStringContainsString('text/markdown', $contentType);
    }

    public function testListsCorePages(): void
    {
        $this->get('/llms.txt');

        $body = $this->getResponse()->getContent() ?: '';

        $this->assertStringContainsString('# baksla.sh', $body);
        $this->assertStringContainsString('https://localhost/', $body);
        $this->assertStringContainsString('https://localhost/blog', $body);
        $this->assertStringContainsString('https://localhost/team', $body);
        $this->assertStringContainsString('https://localhost/legal-notices', $body);
        $this->assertStringContainsString('https://localhost/sitemap.xml', $body);
        $this->assertStringContainsString('https://localhost/blog/feed.xml', $body);
    }

    public function testListsEveryArticleWithMarkdownAlternate(): void
    {
        $articleRepository = $this->getService(ArticleRepository::class);

        $this->get('/llms.txt');

        $body = $this->getResponse()->getContent() ?: '';

        foreach ($articleRepository->findAll() as $article) {
            $this->assertStringContainsString($article->title, $body);
            $this->assertStringContainsString($article->description, $body);
            $this->assertStringContainsString(sprintf('https://localhost/blog/%s', $article->id), $body);
            $this->assertStringContainsString(sprintf('https://localhost/blog/%s.md', $article->id), $body);
        }
    }
}
