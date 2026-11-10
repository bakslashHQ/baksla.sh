<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Rendering;

use App\Blog\Domain\Factory\ArticleFactory\HtmlProvider;
use App\Blog\Infrastructure\ArticleSource;
use Twig\Environment;

final readonly class TwigMarkdownHtmlProvider implements HtmlProvider
{
    private const string METADATA_REGEX = '/\A---\n(\n|.)+?\n---/';

    public function __construct(
        private Environment $twig,
        private LeagueMarkdownConverter $converter,
    ) {
    }

    public function provide(string $id, string $locale): string
    {
        $markdown = $this->twig->render(ArticleSource::template($id, $locale));
        $markdown = preg_replace(self::METADATA_REGEX, '', $markdown) ?? '';

        return $this->converter->convert($markdown);
    }
}
