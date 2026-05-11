<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Rendering;

use App\Blog\Infrastructure\Rendering\CodeBlock\TempestCodeBlockRenderer;
use App\Blog\Infrastructure\Rendering\Emoji\GithubEmojiExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block;
use League\CommonMark\Extension\CommonMark\Node\Inline;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableRenderer;
use League\CommonMark\Extension\Table\TableRow;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Renderer\HtmlDecorator;

final class LeagueMarkdownConverter
{
    private ?\League\CommonMark\MarkdownConverter $converter = null;

    public function __construct(
        private readonly TempestCodeBlockRenderer $codeBlockRenderer,
    ) {
    }

    public function convert(string $markdown): string
    {
        return (string) $this->getConverter()->convert($markdown);
    }

    private function getConverter(): \League\CommonMark\MarkdownConverter
    {
        if (!$this->converter instanceof \League\CommonMark\MarkdownConverter) {
            $environment = new Environment([
                'default_attributes' => [
                    Block\Heading::class => [
                        'class' => static fn (Block\Heading $node): array => match ($node->getLevel()) {
                            1 => throw new \InvalidArgumentException('Titles of level 1 are not accepted, please use the "title" metadata instead.'),
                            2 => [],
                            3 => ['mt-9', 'mb-3', 'font-sans', 'font-bold', 'tracking-tight', 'leading-tight', 'text-2xl', 'sm:text-3xl', 'text-ink', 'text-balance'],
                            default => throw new \InvalidArgumentException(sprintf('Heading level %d is not supported, please use level 2 or 3 only.', $node->getLevel())),
                        },
                    ],
                    Paragraph::class => [
                        'class' => ['my-5'],
                    ],
                    Inline\Strong::class => [
                        'class' => ['font-bold', 'text-ink'],
                    ],
                    Inline\Code::class => [
                        'class' => ['rounded', 'bg-ink/[0.07]', 'px-1', 'py-0.5', 'font-mono', 'text-[0.9em]', 'text-ink', 'hyphens-none'],
                    ],
                    Inline\Image::class => [
                        'class' => ['my-6', 'mx-auto', 'max-w-full', 'h-auto', 'rounded-lg'],
                    ],
                    Inline\Link::class => [
                        'class' => ['text-accent', 'underline', 'underline-offset-2', 'hover:text-ink', 'transition-colors'],
                    ],
                    Block\BlockQuote::class => [
                        'class' => ['my-6', 'pl-5', 'border-l-[3px]', 'border-accent', 'italic', 'text-ink-soft'],
                    ],
                    Block\ListBlock::class => [
                        'class' => static fn (Block\ListBlock $node): array => [
                            'my-5',
                            'pl-5',
                            'sm:pl-7',
                            '[&>li]:mb-2',
                            $node->getListData()->type === 'ordered' ? 'list-decimal' : 'list-disc',
                        ],
                    ],
                    Table::class => [
                        'class' => ['w-full', 'border-collapse', 'text-sm', 'sm:text-base'],
                    ],
                    TableSection::class => [
                        'class' => static fn (TableSection $node): array => $node->getType() === TableSection::TYPE_HEAD
                            ? ['border-b-2', 'border-ink/30']
                            : [],
                    ],
                    TableRow::class => [
                        'class' => ['border-b', 'border-ink/10', 'last:border-b-0'],
                    ],
                    TableCell::class => [
                        'class' => static fn (TableCell $node): array => $node->getType() === TableCell::TYPE_HEADER
                            ? ['px-3', 'sm:px-4', 'py-3', 'text-left', 'align-bottom', 'font-mono', 'text-xs', 'uppercase', 'tracking-widest', 'text-ink-soft']
                            : ['px-3', 'sm:px-4', 'py-3', 'text-left', 'align-top'],
                    ],
                ],
            ]);

            $environment->addExtension(new CommonMarkCoreExtension());
            $environment->addExtension(new DefaultAttributesExtension());
            $environment->addExtension(new GithubFlavoredMarkdownExtension());
            $environment->addExtension(new GithubEmojiExtension());

            $environment->addRenderer(Block\FencedCode::class, $this->codeBlockRenderer);
            $environment->addRenderer(Table::class, new HtmlDecorator(new TableRenderer(), 'div', [
                'class' => 'my-6 -mx-4 sm:mx-0 px-4 sm:px-0 overflow-x-auto',
            ]));

            $this->converter = new \League\CommonMark\MarkdownConverter($environment);
        }

        return $this->converter;
    }
}
