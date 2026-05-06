<?php

declare(strict_types=1);

namespace App\Website\Infrastructure\Rendering\Components;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'website:CommandPalette', template: 'components/website/CommandPalette.html.twig')]
final readonly class CommandPalette
{
    public function __construct(
        private ArticlePreviewRepository $articlePreviewRepository,
    ) {
    }

    public function getShowcasedArticle(): ?ArticlePreview
    {
        return $this->articlePreviewRepository->findShowcased();
    }
}
