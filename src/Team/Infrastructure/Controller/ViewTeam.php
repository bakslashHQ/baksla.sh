<?php

declare(strict_types=1);

namespace App\Team\Infrastructure\Controller;

use App\Shared\Infrastructure\StaticSiteGeneration\Prerender;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
final readonly class ViewTeam
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    #[Route(path: '/team', name: 'app_team', methods: ['GET'])]
    #[Prerender]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('pages/team/index.html.twig'));
    }
}
