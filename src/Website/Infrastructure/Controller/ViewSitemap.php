<?php

declare(strict_types=1);

namespace App\Website\Infrastructure\Controller;

use App\Blog\Domain\Repository\ArticleRepository;
use App\Blog\Infrastructure\Routing\ArticleUrlGenerator;
use Bakslash\StaticSiteBundle\Attribute\Prerender;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsController]
final readonly class ViewSitemap
{
    /**
     * @param list<string> $enabledLocales
     */
    public function __construct(
        private ArticleRepository $articleRepository,
        private ArticleUrlGenerator $articleUrlGenerator,
        private UrlGeneratorInterface $urlGenerator,
        #[Autowire(param: 'kernel.enabled_locales')]
        private array $enabledLocales,
    ) {
    }

    /**
     * @return array{urls: list<array{loc: string, alternates: array<string, string>, lastmod?: string}>}
     */
    #[Route(path: 'sitemap.xml', name: 'app_sitemap', methods: [Request::METHOD_GET], format: 'xml')]
    #[Prerender]
    #[Template('pages/website/sitemap.xml.twig')]
    public function __invoke(): array
    {
        $urls = [];

        foreach (['app_home', 'app_blog', 'app_team', 'app_legal_notices'] as $route) {
            $alternates = [];
            foreach ($this->enabledLocales as $locale) {
                $alternates[$locale] = $this->urlGenerator->generate($route, [
                    '_locale' => $locale,
                ], UrlGeneratorInterface::ABSOLUTE_URL);
            }

            foreach ($alternates as $loc) {
                $urls[] = [
                    'loc' => $loc,
                    'alternates' => $alternates,
                ];
            }
        }

        foreach ($this->articleRepository->findAll() as $article) {
            $alternates = $this->articleUrlGenerator->urlsByLocale($article);

            $lastmod = $article->publishedAt->format('Y-m-d');
            foreach ($alternates as $loc) {
                $urls[] = [
                    'loc' => $loc,
                    'alternates' => $alternates,
                    'lastmod' => $lastmod,
                ];
            }
        }

        return [
            'urls' => $urls,
        ];
    }
}
