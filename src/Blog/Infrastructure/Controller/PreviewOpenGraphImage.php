<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Controller;

use App\Blog\Domain\Exception\MissingArticleException;
use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class PreviewOpenGraphImage
{
    public function __construct(
        private ArticleRepository $articleRepository,
        #[Autowire(service: 'profiler')]
        private ?Profiler $profiler = null,
    ) {
    }

    /**
     * @return array{article: Article}
     */
    #[Route(path: '/_og/blog/{id<[a-z0-9-]+>}', name: 'app_blog_og_preview', methods: ['GET'])]
    #[Template('og_image/article.html.twig')]
    public function __invoke(string $id): array
    {
        $this->profiler?->disable();

        try {
            $article = $this->articleRepository->get($id);
        } catch (MissingArticleException) {
            throw new NotFoundHttpException(sprintf('Article "%s" not found', $id));
        }

        return [
            'article' => $article,
        ];
    }
}
