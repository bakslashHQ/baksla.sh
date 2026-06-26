<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Controller;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use Bakslash\StaticSiteBundle\Attribute\Prerender;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class ViewBlog
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {
    }

    /**
     * @return array{latest: ?Article, articles: list<Article>}
     */
    #[Route(path: [
        'en' => '/blog',
        'fr' => '/fr/blog',
    ], name: 'app_blog', methods: [Request::METHOD_GET])]
    #[Prerender]
    #[Template('pages/blog/index.html.twig')]
    public function __invoke(): array
    {
        $articles = $this->articleRepository->findAll();
        $latest = $this->articleRepository->findLatest();

        if ($articles !== [] && $latest instanceof Article) {
            $articles = array_values(array_filter($articles, static fn (Article $a): bool => $a->id !== $latest->id));
        }

        return [
            'latest' => $latest,
            'articles' => $articles,
        ];
    }
}
