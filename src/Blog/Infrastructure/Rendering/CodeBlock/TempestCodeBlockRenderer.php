<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Rendering\CodeBlock;

use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tempest\Highlight\Highlighter;
use Tempest\Highlight\Themes\CssTheme;

final class TempestCodeBlockRenderer implements NodeRendererInterface
{
    private const string PRE_CLASSES = 'notranslate my-6 px-4 py-4 sm:px-6 sm:py-5 rounded-xl bg-ink text-paper font-mono text-[0.8125rem] sm:text-[0.875rem] leading-[1.65] overflow-x-auto focus:outline-none focus-visible:ring-2 focus-visible:ring-accent';

    private ?Highlighter $highlighter = null;

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        $this->highlighter ??= new Highlighter(new CssTheme());

        if (!$node instanceof FencedCode) {
            throw new \InvalidArgumentException(sprintf('Block must be instance of "%s".', FencedCode::class));
        }

        preg_match('/^(?<language>[\w]+)/', $node->getInfoWords()[0] ?? 'txt', $matches);
        $language = $matches['language'] ?? 'txt';

        $parsed = $this->highlighter->parse($node->getLiteral(), $language);
        $ariaLabel = $this->translator->trans('blog.article.code_sample', [
            'language' => $language,
        ]);

        return sprintf(
            '<pre data-lang="%s" tabindex="0" aria-label="%s" class="%s">%s</pre>',
            htmlspecialchars($language, ENT_QUOTES),
            htmlspecialchars($ariaLabel, ENT_QUOTES),
            self::PRE_CLASSES,
            $parsed,
        );
    }
}
