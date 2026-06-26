<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Controller;

use App\Blog\Domain\Exception\MissingArticleException;
use App\Blog\Domain\Repository\ArticleRepository;
use App\Blog\Infrastructure\Routing\ArticleUrlGenerator;
use App\Blog\Infrastructure\StaticSiteGeneration\BlogArticleParamsProvider;
use App\Shared\Infrastructure\StaticSiteGeneration\Prerender;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\LocaleSwitcher;
use Twig\Environment;

#[AsController]
final readonly class ViewArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private ArticleUrlGenerator $articleUrlGenerator,
        private Environment $twig,
        private LocaleSwitcher $localeSwitcher,
    ) {
    }

    #[Route(path: [
        'en' => '/blog/{slug<[a-z0-9-]+>}.{_format<md>?html}',
        'fr' => '/fr/blog/{slug<[a-z0-9-]+>}.{_format<md>?html}',
    ], name: 'app_blog_article', methods: ['GET'])]
    #[Prerender(params: BlogArticleParamsProvider::class)]
    public function __invoke(string $slug, Request $request): Response
    {
        try {
            $article = $this->articleRepository->getBySlug($slug);
        } catch (MissingArticleException) {
            throw new NotFoundHttpException();
        }

        if ($request->getRequestFormat() === 'md') {
            return new Response(
                $this->twig->render(sprintf('articles/%s.%s.md.twig', $article->id, $this->localeSwitcher->getLocale())),
                headers: [
                    'Content-Type' => 'text/markdown; charset=utf-8',
                ],
            );
        }

        return new Response($this->twig->render('pages/blog/article.html.twig', [
            'article' => $article,
            'hreflang_alternates' => $this->articleUrlGenerator->urlsByLocale($article),
        ]));
    }
}
