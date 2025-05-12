<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Rendering\Emoji;

use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use Symfony\Component\Emoji\EmojiTransliterator;

final class GitHubEmojiInlineParser implements InlineParserInterface
{
    private ?EmojiTransliterator $emojiTransliterator = null;

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex(':[a-z0-9\+\-_]+:');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $inlineContext->getCursor()->advanceBy($inlineContext->getFullMatchLength());

        $match = $inlineContext->getFullMatch();

        $inlineContext->getContainer()->appendChild(new Text($this->getTransliterator()->transliterate($match) ?: $match));

        return true;
    }

    private function getTransliterator(): EmojiTransliterator
    {
        return $this->emojiTransliterator ??= EmojiTransliterator::create('text-emoji');
    }
}
