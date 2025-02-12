<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Twig\Components;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final readonly class BlogOverview
{
    public function __construct(
        private ArticlePreviewRepository $articlePreviewRepository,
    ) {
    }

    public function getShowcased(): ?ArticlePreview
    {
        return $this->articlePreviewRepository->findShowcased();
    }
}
