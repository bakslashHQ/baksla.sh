<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Rendering\CodeBlock;

use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Tempest\Highlight\Highlighter;
use Tempest\Highlight\Themes\CssTheme;
use Tempest\Highlight\WebTheme;

final class TempestCodeBlockRenderer implements NodeRendererInterface
{
    private ?Highlighter $highlighter = null;

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        $this->highlighter ??= new Highlighter(new CssTheme());

        if (!$node instanceof FencedCode) {
            throw new \InvalidArgumentException(sprintf('Block must be instance of "%s".', FencedCode::class));
        }

        preg_match('/^(?<language>[\w]+)/', $node->getInfoWords()[0] ?? 'txt', $matches);
        $language = $matches['language'] ?? 'txt';

        /** @var WebTheme $theme */
        $theme = $this->highlighter->getTheme();
        $parsed = $this->highlighter->parse($node->getLiteral(), $language);

        return sprintf(
            '<div class="code">%s%s%s</div>',
            $theme->preBefore($this->highlighter),
            $parsed,
            $theme->preAfter($this->highlighter),
        );
    }
}
