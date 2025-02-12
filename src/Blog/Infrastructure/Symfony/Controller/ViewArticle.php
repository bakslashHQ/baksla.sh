<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Symfony\Controller;

use App\Blog\Domain\Exception\MissingArticleException;
use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
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

    #[Route(name: 'app_blog_article', path: '/blog/{id}', methods: ['GET'])]
    public function __invoke(Request $request, string $id): Response
    {
        $response = new Response();

        try {
            $article = $this->articleRepository->get($id);
        } catch (MissingArticleException) {
            throw new NotFoundHttpException();
        }

        $response->setEtag($article->hash . $this->articlePreviewRepository->getHash());
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        $articles = $this->articlePreviewRepository->findAll();
        if ($articles !== []) {
            $articles = array_values(array_filter($articles, static fn (ArticlePreview $a): bool => $a->id !== $article->id));
        }

        $more = [];
        $keys = $articles !== [] ? array_rand($articles, min(self::MORE_COUNT, \count($articles))) : [];

        for ($i = 0; $i < self::MORE_COUNT; ++$i) {
            $key = $keys[$i] ?? null;
            $more[] = $key !== null ? $articles[$key] : null;
        }

        $response->setContent($this->twig->render('pages/blog/article.html.twig', [
            'article' => $article,
            'more' => $more,
        ]));

        return $response;
    }
}
