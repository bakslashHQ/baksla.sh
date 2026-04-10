<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsController]
final readonly class ViewBlog
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route(path: '/blog', name: 'app_blog', methods: ['GET'])]
    public function __invoke(): RedirectResponse
    {
        return new RedirectResponse(
            $this->urlGenerator->generate('app_home') . '#blog',
            301,
        );
    }
}
