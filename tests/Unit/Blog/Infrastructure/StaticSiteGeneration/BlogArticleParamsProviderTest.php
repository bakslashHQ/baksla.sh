<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\StaticSiteGeneration;

use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Blog\Infrastructure\StaticSiteGeneration\BlogArticleParamsProvider;
use PHPUnit\Framework\TestCase;

final class BlogArticleParamsProviderTest extends TestCase
{
    public function testProvideParams(): void
    {
        $repository = $this->createStub(ArticlePreviewRepository::class);
        $repository->method('findAll')->willReturn([
            anArticlePreview()->withId('article-1')->build(),
            anArticlePreview()->withId('article-2')->build(),
        ]);

        $provider = new BlogArticleParamsProvider($repository);

        $this->assertSame([
            [
                'id' => 'article-1',
            ],
            [
                'id' => 'article-2',
            ],
        ], iterator_to_array($provider->provideParams()));
    }
}
