<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Controller;

use App\Blog\Domain\Exception\MissingArticleException;
use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Blog\Domain\Repository\ArticleRepository;
use App\Blog\Infrastructure\StaticSiteGeneration\BlogArticleParamsProvider;
use App\Shared\Infrastructure\StaticSiteGeneration\Prerender;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
final readonly class ViewArticle
{
    private const int MORE_COUNT = 3;

    public function __construct(
        private ArticleRepository $articleRepository,
        private ArticlePreviewRepository $articlePreviewRepository,
        private Environment $twig,
    ) {
    }

    #[Route(path: '/blog/{id}', name: 'app_blog_article', methods: ['GET'])]
    #[Prerender(params: BlogArticleParamsProvider::class)]
    public function __invoke(string $id): Response
    {
        try {
            $article = $this->articleRepository->get($id);
        } catch (MissingArticleException) {
            throw new NotFoundHttpException();
        }

        $articles = $this->articlePreviewRepository->findAll();
        if ($articles !== []) {
            $articles = array_values(array_filter($articles, static fn (ArticlePreview $a): bool => $a->id !== $article->id));
        }

        $more = \array_slice($articles, 0, self::MORE_COUNT);
        $more = array_pad($more, self::MORE_COUNT, null);

        return new Response($this->twig->render('pages/blog/article.html.twig', [
            'article' => $article,
            'more' => $more,
        ]));
    }
}
