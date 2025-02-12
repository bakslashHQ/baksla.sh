<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Domain\Factory;

use App\Blog\Domain\Factory\ArticleFactory;
use App\Blog\Domain\Factory\ArticleFactory\HtmlGenerator;
use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use PHPUnit\Framework\TestCase;

final class ArticleFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $preview = anArticlePreview()->build();

        $articlePreviewRepository = $this->createStub(ArticlePreviewRepository::class);
        $articlePreviewRepository->method('get')->willReturn($preview);

        $htmlGenerator = $this->createStub(HtmlGenerator::class);
        $htmlGenerator->method('generate')->willReturn('html');

        $article = (new ArticleFactory($articlePreviewRepository, $htmlGenerator))->create($preview->id);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertSame($preview->id, $article->id);
        $this->assertSame($preview->title, $article->title);
        $this->assertSame($preview->description, $article->description);
        $this->assertSame($preview->author, $article->author);
        $this->assertSame('html', $article->html);
    }
}
