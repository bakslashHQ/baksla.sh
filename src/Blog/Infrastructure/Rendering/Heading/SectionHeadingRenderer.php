<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Rendering\Heading;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Renderer\Block\HeadingRenderer;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

final readonly class SectionHeadingRenderer implements NodeRendererInterface
{
    private HeadingRenderer $inner;

    public function __construct()
    {
        $this->inner = new HeadingRenderer();
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
    {
        if (!$node instanceof Heading) {
            throw new \InvalidArgumentException(sprintf('Block must be instance of "%s".', Heading::class));
        }

        $heading = $this->inner->render($node, $childRenderer);

        // h2 is a grid: without the span, its text and inline code would each get their own cell
        if ($node->getLevel() === 2 && $heading instanceof HtmlElement) {
            $heading->setContents(new HtmlElement('span', [], $heading->getContents(false)));
        }

        return $heading;
    }
}
