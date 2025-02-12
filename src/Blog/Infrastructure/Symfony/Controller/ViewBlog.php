<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Symfony\Controller;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
final readonly class ViewBlog
{
    public function __construct(
        private ArticlePreviewRepository $articlePreviewRepository,
        private Environment $twig,
    ) {
    }

    #[Route(name: 'app_blog', path: '/blog', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $response = new Response();
        $response->setEtag($this->articlePreviewRepository->getHash());
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        $articles = $this->articlePreviewRepository->findAll();
        $showcased = $this->articlePreviewRepository->findShowcased();

        if ($articles !== [] && $showcased instanceof ArticlePreview) {
            $articles = array_values(array_filter($articles, static fn (ArticlePreview $a): bool => $a->id !== $showcased->id));
        }

        $response->setContent($this->twig->render('pages/blog/index.html.twig', [
            'showcased' => $showcased,
            'articles' => $articles,
        ]));

        return $response;
    }
}
