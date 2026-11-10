<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\OpenGraph;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Attribute\AsTwigFunction;

final readonly class OpenGraphImagePath
{
    public function __construct(
        #[Autowire(param: 'kernel.default_locale')]
        private string $defaultLocale,
    ) {
    }

    /**
     * Public path of the OG image of an article, relative to the public directory.
     */
    #[AsTwigFunction('article_og_image_path')]
    public function of(string $articleId, string $locale): string
    {
        // The default locale keeps the original "{id}.jpg" path for backward compatibility
        return $locale === $this->defaultLocale
            ? sprintf('/open-graph/article/%s.jpg', $articleId)
            : sprintf('/open-graph/article/%s.%s.jpg', $articleId, $locale);
    }
}
