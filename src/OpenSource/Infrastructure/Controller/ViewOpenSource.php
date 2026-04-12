<?php

declare(strict_types=1);

namespace App\OpenSource\Infrastructure\Controller;

use App\Shared\Infrastructure\StaticSiteGeneration\Prerender;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
final readonly class ViewOpenSource
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    #[Route(path: '/open-source', name: 'app_open_source', methods: ['GET'])]
    #[Prerender]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('pages/open-source/index.html.twig'));
    }
}
