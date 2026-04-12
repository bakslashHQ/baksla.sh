<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Controller;

use App\Shared\Infrastructure\StaticSiteGeneration\Prerender;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
final readonly class ViewBlog
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    #[Route(path: '/blog', name: 'app_blog', methods: ['GET'])]
    #[Prerender]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('pages/blog/index.html.twig'));
    }
}
