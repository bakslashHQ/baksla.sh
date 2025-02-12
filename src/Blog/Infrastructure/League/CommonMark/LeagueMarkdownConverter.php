<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\League\CommonMark;

use App\Blog\Infrastructure\Symfony\Emoji\GithubEmojiExtension;
use App\Blog\Infrastructure\Tempest\Highlight\TempestCodeBlockRenderer;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block;
use League\CommonMark\Extension\CommonMark\Node\Inline;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Node\Block\Paragraph;

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
                    Paragraph::class => [
                        'class' => static function (Paragraph $node): array {
                            if (!$node->next() instanceof Block\Heading) {
                                return ['mb-6'];
                            }

                            return [];
                        },
                    ],
                    Inline\Code::class => [
                        'class' => ['bg-gray-100', 'px-1', 'py-0.5', 'rounded'],
                    ],
                    Inline\Strong::class => [
                        'class' => ['font-semibold'],
                    ],
                    Inline\Link::class => [
                        'class' => ['text-blue-700', 'underline', 'underline-offset-4'],
                    ],
                    Inline\Image::class => [
                        'class' => ['rounded-lg', 'shadow-lg', 'mx-auto', 'my-20'],
                    ],
                    Block\BlockQuote::class => [
                        'class' => ['border-s-4', 'border-gray-300', 'px-4', 'my-6', 'text-gray-600'],
                    ],
                    Block\Heading::class => [
                        'class' => static function (Block\Heading $node): array {
                            $borderClasses = match ($node->getLevel()) {
                                1 => throw new \InvalidArgumentException('Titles of level 1 are not accepted, please use the "title" metadata instead.'),
                                2 => ['border-b', 'border-indigo-500/50', 'border-b-8'],
                                default => [],
                            };

                            $sizeClasses = match ($node->getLevel()) {
                                2 => ['text-3xl'],
                                default => ['text-2xl'],
                            };

                            $marginClasses = match ($node->getLevel()) {
                                2 => ['mb-8'],
                                default => ['mb-4'],
                            };

                            if (!$node->previous() instanceof Block\Heading) {
                                $marginClasses = [
                                    ...$marginClasses,
                                    ...match ($node->getLevel()) {
                                        2 => ['mt-10', 'sm:mt-20'],
                                        default => ['mt-10'],
                                    },
                                ];
                            }

                            return ['text-pretty', 'font-bold', 'text-gray-800', ...$borderClasses, ...$marginClasses, ...$sizeClasses];
                        },
                    ],
                    Block\ListBlock::class => [
                        'class' => static function (Block\ListBlock $node): array {
                            $listBulletClass = match (true) {
                                $node->getListData()->type === 'ordered' => 'list-decimal',
                                $node->getListData()->bulletChar === '-' => 'list-disc',
                                default => '',
                            };

                            $marginClass = $node->next() instanceof Block\ListBlock ? 'mb-3' : 'mb-6';

                            return [$listBulletClass, $marginClass, 'pl-12'];
                        },
                    ],
                ],
            ]);

            $environment->addExtension(new CommonMarkCoreExtension());
            $environment->addExtension(new DefaultAttributesExtension());
            $environment->addExtension(new GithubFlavoredMarkdownExtension());
            $environment->addExtension(new GithubEmojiExtension());

            $environment->addRenderer(Block\FencedCode::class, $this->codeBlockRenderer);

            $this->converter = new \League\CommonMark\MarkdownConverter($environment);
        }

        return $this->converter;
    }
}
