<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\StaticSiteGeneration;

use App\Blog\Domain\Repository\ArticleRepository;
use App\Blog\Infrastructure\StaticSiteGeneration\BlogArticleParamsProvider;
use PHPUnit\Framework\TestCase;

final class BlogArticleParamsProviderTest extends TestCase
{
    public function testProvideParams(): void
    {
        $repository = $this->createStub(ArticleRepository::class);
        $repository->method('findAll')->willReturn([
            anArticle()->withSlug('article-1-slug')->build(),
            anArticle()->withSlug('article-2-slug')->build(),
        ]);

        $provider = new BlogArticleParamsProvider($repository);

        $this->assertSame([
            [
                'slug' => 'article-1-slug',
            ],
            [
                'slug' => 'article-2-slug',
            ],
        ], iterator_to_array($provider->provideParams()));
    }
}
