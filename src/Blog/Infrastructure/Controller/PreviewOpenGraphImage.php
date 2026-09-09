<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Controller;

use App\Blog\Domain\Exception\MissingArticleException;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
final readonly class PreviewOpenGraphImage
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private Environment $twig,
        #[Autowire(service: 'profiler')]
        private ?Profiler $profiler = null,
    ) {
    }

    #[Route(path: [
        'en' => '/_og/blog/{id<[a-z0-9-]+>}',
        'fr' => '/fr/_og/blog/{id<[a-z0-9-]+>}',
    ], name: 'app_blog_og_preview', methods: ['GET'])]
    public function __invoke(string $id): Response
    {
        $this->profiler?->disable();

        try {
            $article = $this->articleRepository->get($id);
        } catch (MissingArticleException) {
            throw new NotFoundHttpException(sprintf('Article "%s" not found', $id));
        }

        return new Response($this->twig->render('og_image/article.html.twig', [
            'article' => $article,
        ]));
    }
}
