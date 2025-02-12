<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Twig;

use App\Blog\Domain\Factory\ArticleFactory\HtmlGenerator;
use App\Blog\Infrastructure\League\CommonMark\LeagueMarkdownConverter;
use Twig\Environment;

final readonly class TwigMarkdownHtmlGenerator implements HtmlGenerator
{
    private const string METADATA_REGEX = '/\A---\n(\n|.)+?\n---/';

    public function __construct(
        private Environment $twig,
        private LeagueMarkdownConverter $converter,
    ) {
    }

    public function generate(string $id): string
    {
        $markdown = $this->twig->render(sprintf('articles/%s.md.twig', $id));
        $markdown = preg_replace(self::METADATA_REGEX, '', $markdown) ?? '';

        return $this->converter->convert($markdown);
    }
}
