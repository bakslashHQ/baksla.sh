<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Routing;

use App\Blog\Domain\Exception\MissingArticleException;
use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\LocaleSwitcher;

final readonly class ArticleUrlGenerator
{
    /**
     * @param list<string> $enabledLocales
     */
    public function __construct(
        private ArticleRepository $articleRepository,
        private UrlGeneratorInterface $urlGenerator,
        private LocaleSwitcher $localeSwitcher,
        #[Autowire(param: 'kernel.enabled_locales')]
        private array $enabledLocales,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function urlsByLocale(Article $article): array
    {
        $urls = [];
        foreach ($this->enabledLocales as $locale) {
            $slug = $this->slugForLocale($article, $locale);
            if ($slug === null) {
                continue;
            }

            $urls[$locale] = $this->urlGenerator->generate('app_blog_article', [
                'slug' => $slug,
                '_locale' => $locale,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $urls;
    }

    private function slugForLocale(Article $article, string $locale): ?string
    {
        if ($locale === $this->localeSwitcher->getLocale()) {
            return $article->slug;
        }

        return $this->localeSwitcher->runWithLocale($locale, function () use ($article): ?string {
            try {
                return $this->articleRepository->get($article->id)->slug;
            } catch (MissingArticleException) {
                return null;
            }
        });
    }
}
