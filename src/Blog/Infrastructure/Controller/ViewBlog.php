<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Controller;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use App\Shared\Infrastructure\StaticSiteGeneration\Prerender;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
final readonly class ViewBlog
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private Environment $twig,
    ) {
    }

    #[Route(path: '/blog', name: 'app_blog', methods: ['GET'])]
    #[Prerender]
    public function __invoke(): Response
    {
        $articles = $this->articleRepository->findAll();
        $latest = $this->articleRepository->findLatest();

        if ($articles !== [] && $latest instanceof Article) {
            $articles = array_values(array_filter($articles, static fn (Article $a): bool => $a->id !== $latest->id));
        }

        return new Response($this->twig->render('pages/blog/index.html.twig', [
            'latest' => $latest,
            'articles' => $articles,
        ]));
    }
}
