<?php

declare(strict_types=1);

namespace App\Home\Infrastructure\Rendering\Components;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Home:BlogOverview', template: 'components/Home/BlogOverview.html.twig')]
final class BlogOverview
{
    private const int OTHERS_LIMIT = 2;

    public function __construct(
        private readonly ArticleRepository $articleRepository,
    ) {
    }

    public function getShowcased(): ?Article
    {
        return $this->articleRepository->findShowcased();
    }

    /**
     * @return list<Article>
     */
    public function getOthers(): array
    {
        $showcased = $this->articleRepository->findShowcased();
        $articles = $this->articleRepository->findAll();

        if ($showcased instanceof Article) {
            $articles = array_filter($articles, static fn (Article $a): bool => $a->id !== $showcased->id);
        }

        return \array_slice(array_values($articles), 0, self::OTHERS_LIMIT);
    }
}
