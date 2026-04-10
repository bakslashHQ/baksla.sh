<?php

declare(strict_types=1);

namespace App\Team\Infrastructure\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsController]
final readonly class ViewTeam
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route(path: '/team', name: 'app_team', methods: ['GET'])]
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse(
            $this->urlGenerator->generate('app_home') . '#team',
            301,
        );
    }
}
