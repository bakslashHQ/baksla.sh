<?php

declare(strict_types=1);

namespace App\Website\Infrastructure\Controller;

use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Shared\Infrastructure\StaticSiteGeneration\Prerender;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
final readonly class ViewLlms
{
    public function __construct(
        private ArticlePreviewRepository $articlePreviewRepository,
        private Environment $twig,
    ) {
    }

    #[Route(path: '/llms.txt', name: 'app_llms', methods: ['GET'], format: 'txt')]
    #[Prerender]
    public function __invoke(): Response
    {
        return new Response(
            $this->twig->render('pages/website/llms.txt.twig', [
                'articles' => $this->articlePreviewRepository->findAll(),
            ]),
            headers: [
                'Content-Type' => 'text/markdown; charset=utf-8',
            ],
        );
    }
}
