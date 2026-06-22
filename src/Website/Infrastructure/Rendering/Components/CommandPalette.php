<?php

declare(strict_types=1);

namespace App\Website\Infrastructure\Rendering\Components;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'website:CommandPalette', template: 'components/website/CommandPalette.html.twig')]
final class CommandPalette
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
    ) {
    }

    public function getShowcasedArticle(): ?Article
    {
        return $this->articleRepository->findShowcased();
    }
}
