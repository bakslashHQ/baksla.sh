<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\StaticSiteGeneration;

use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Shared\Infrastructure\StaticSiteGeneration\ParamsProviderInterface;

final readonly class BlogArticleParamsProvider implements ParamsProviderInterface
{
    public function __construct(
        private ArticlePreviewRepository $articlePreviewRepository,
    ) {
    }

    public function provideParams(): iterable
    {
        foreach ($this->articlePreviewRepository->findAll() as $article) {
            yield [
                'id' => $article->id,
            ];
        }
    }
}
