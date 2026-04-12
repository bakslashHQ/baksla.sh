<?php

declare(strict_types=1);

namespace App\Website\Infrastructure\Rendering\Components;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final readonly class Blog
{
    public function __construct(
        private ArticlePreviewRepository $articlePreviewRepository,
    ) {
    }

    public function getShowcased(): ?ArticlePreview
    {
        return $this->articlePreviewRepository->findShowcased();
    }

    /**
     * @return list<ArticlePreview>
     */
    public function getArticles(): array
    {
        $articles = $this->articlePreviewRepository->findAll();
        $showcased = $this->getShowcased();

        if ($articles !== [] && $showcased instanceof ArticlePreview) {
            return array_values(array_filter($articles, static fn (ArticlePreview $a): bool => $a->id !== $showcased->id));
        }

        return $articles;
    }
}
