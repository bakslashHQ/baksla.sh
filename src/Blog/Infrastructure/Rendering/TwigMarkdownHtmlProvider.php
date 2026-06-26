<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Rendering;

use App\Blog\Domain\Factory\ArticleFactory\HtmlProvider;
use Symfony\Component\Translation\LocaleSwitcher;
use Twig\Environment;

final readonly class TwigMarkdownHtmlProvider implements HtmlProvider
{
    private const string METADATA_REGEX = '/\A---\n(\n|.)+?\n---/';

    public function __construct(
        private Environment $twig,
        private LeagueMarkdownConverter $converter,
        private LocaleSwitcher $localeSwitcher,
    ) {
    }

    public function provide(string $id): string
    {
        $markdown = $this->twig->render(sprintf('articles/%s.%s.md.twig', $id, $this->localeSwitcher->getLocale()));
        $markdown = preg_replace(self::METADATA_REGEX, '', $markdown) ?? '';

        return $this->converter->convert($markdown);
    }
}
