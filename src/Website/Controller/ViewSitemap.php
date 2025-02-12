<?php

declare(strict_types=1);

namespace App\Website\Controller;

use App\Blog\Domain\Repository\ArticlePreviewRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

#[AsController]
final readonly class ViewSitemap
{
    public function __construct(
        private ArticlePreviewRepository $articlePreviewRepository,
        private UrlGeneratorInterface $urlGenerator,
        private Environment $twig,
    ) {
    }

    #[Route(name: 'app_sitemap', path: 'sitemap.xml', methods: ['GET'], format: 'xml')]
    public function __invoke(): Response
    {
        $urls = [
            [
                'loc' => $this->urlGenerator->generate('app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'loc' => $this->urlGenerator->generate('app_blog', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];
        foreach ($this->articlePreviewRepository->findAll() as $preview) {
            $urls[] = [
                'loc' => $this->urlGenerator->generate('app_blog_article', [
                    'id' => $preview->id,
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ];
        }

        return new Response($this->twig->render('pages/website/sitemap.xml.twig', [
            'urls' => $urls,
        ]));
    }
}
