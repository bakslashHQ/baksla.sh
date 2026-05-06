<?php

declare(strict_types=1);

namespace App\Home\Infrastructure\Rendering\Components;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Home:BlogOverview', template: 'components/Home/BlogOverview.html.twig')]
final readonly class BlogOverview
{
    private const int OTHERS_LIMIT = 3;

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
    public function getOthers(): array
    {
        return $this->articlePreviewRepository->findLatest(self::OTHERS_LIMIT, excludeShowcased: true);
    }
}
