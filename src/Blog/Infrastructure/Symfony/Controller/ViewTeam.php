<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Symfony\Controller;

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

    #[Route(name: 'app_team', path: '/team', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('pages/team/index.html.twig'));
    }
}
