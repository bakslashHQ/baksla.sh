<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\StaticSiteGeneration;

use App\Blog\Domain\Repository\ArticleRepository;
use App\Shared\Infrastructure\StaticSiteGeneration\ParamsProviderInterface;

final readonly class BlogArticleParamsProvider implements ParamsProviderInterface
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {
    }

    public function provideParams(): iterable
    {
        foreach ($this->articleRepository->findAll() as $article) {
            yield [
                'slug' => $article->slug,
            ];
        }
    }
}
