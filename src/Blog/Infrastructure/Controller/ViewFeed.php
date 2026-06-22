<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Controller;

use App\Blog\Domain\Repository\ArticleRepository;
use App\Shared\Infrastructure\StaticSiteGeneration\Prerender;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
final readonly class ViewFeed
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private Environment $twig,
    ) {
    }

    #[Route(path: '/blog/feed.xml', name: 'app_blog_feed', methods: ['GET'], format: 'xml')]
    #[Prerender]
    public function __invoke(): Response
    {
        return new Response(
            $this->twig->render('pages/blog/feed.xml.twig', [
                'articles' => $this->articleRepository->findAll(),
            ]),
            headers: [
                'Content-Type' => 'application/atom+xml; charset=utf-8',
            ],
        );
    }
}
