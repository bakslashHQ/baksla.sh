<?php

declare(strict_types=1);

namespace App\Home\Infrastructure\Rendering\Components;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Home:BlogOverview', template: 'components/Home/BlogOverview.html.twig')]
final class BlogOverview
{
    private const int OTHERS_LIMIT = 2;

    public function __construct(
        private readonly ArticlePreviewRepository $articlePreviewRepository,
    ) {
    }

    public function getShowcased(): ?ArticlePreview
    {
        return $this->articlePreviewRepository->findShowcased();
    }

    /**
     * @return list<ArticlePreview>
     */
    public function getOthers(): array
    {
        $showcased = $this->articlePreviewRepository->findShowcased();
        $previews = $this->articlePreviewRepository->findAll();

        if ($showcased instanceof ArticlePreview) {
            $previews = array_filter($previews, static fn (ArticlePreview $p): bool => $p->id !== $showcased->id);
        }

        return \array_slice(array_values($previews), 0, self::OTHERS_LIMIT);
    }
}
