<?php

declare(strict_types=1);

namespace App\Website\Infrastructure\Controller;

use App\Blog\Domain\Repository\ArticleRepository;
use Bakslash\StaticSiteBundle\Attribute\Prerender;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsController]
final readonly class ViewSitemap
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @return array{urls: list<array{loc: string, lastmod?: string}>}
     */
    #[Route(path: 'sitemap.xml', name: 'app_sitemap', methods: ['GET'], format: 'xml')]
    #[Prerender]
    #[Template('pages/website/sitemap.xml.twig')]
    public function __invoke(): array
    {
        $urls = [
            [
                'loc' => $this->urlGenerator->generate('app_home', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'loc' => $this->urlGenerator->generate('app_blog', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'loc' => $this->urlGenerator->generate('app_team', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'loc' => $this->urlGenerator->generate('app_legal_notices', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];
        foreach ($this->articleRepository->findAll() as $article) {
            $urls[] = [
                'loc' => $this->urlGenerator->generate('app_blog_article', [
                    'slug' => $article->slug,
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => $article->publishedAt->format('Y-m-d'),
            ];
        }

        return [
            'urls' => $urls,
        ];
    }
}
