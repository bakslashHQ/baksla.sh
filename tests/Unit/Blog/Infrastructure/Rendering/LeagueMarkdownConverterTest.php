<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\League\CommonMark;

use App\Blog\Infrastructure\Rendering\CodeBlock\TempestCodeBlockRenderer;
use App\Blog\Infrastructure\Rendering\LeagueMarkdownConverter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Translation\IdentityTranslator;

final class LeagueMarkdownConverterTest extends TestCase
{
    /**
     * @param list<string> $expectedClasses
     */
    #[DataProvider('elementClassesDataProvider')]
    public function testElementHasClasses(string $selector, array $expectedClasses, string $markdown): void
    {
        $crawler = new Crawler($this->convert($markdown));
        $element = $crawler->filter($selector);

        $this->assertGreaterThan(0, $element->count(), sprintf('Selector "%s" matched no element.', $selector));

        $classes = preg_split('/\s+/', trim((string) $element->first()->attr('class'))) ?: [];

        foreach ($expectedClasses as $expected) {
            $this->assertContains($expected, $classes, sprintf('Class "%s" missing on <%s>.', $expected, $selector));
        }
    }

    /**
     * @return iterable<string, array{0: string, 1: list<string>, 2: string}>
     */
    public static function elementClassesDataProvider(): iterable
    {
        yield 'paragraph' => ['p', ['my-5'], 'foo'];
        yield 'h3 has bold and size classes' => ['h3', ['font-bold', 'text-2xl', 'sm:text-3xl', 'text-balance'], '### title'];
        yield 'unordered list' => ['ul', ['list-disc', 'my-5', 'pl-5'], '- foo'];
        yield 'ordered list' => ['ol', ['list-decimal', 'my-5'], '1. foo'];
        yield 'strong' => ['strong', ['font-bold', 'text-ink'], '**foo**'];
        yield 'inline code is tinted and not hyphenated' => ['code', ['bg-ink/[0.07]', 'rounded', 'hyphens-none'], '`foo`'];
        yield 'link' => ['a', ['text-accent', 'underline'], '[foo](https://example.com)'];
        yield 'blockquote' => ['blockquote', ['border-accent', 'border-l-[3px]', 'italic'], '> foo'];
        yield 'code block pre is dark and rounded' => ['pre', ['bg-ink', 'text-paper', 'rounded-xl'], "```php\necho 'hi';\n```"];
        yield 'table is full-width and collapsed' => ['table', ['w-full', 'border-collapse'], "| a | b |\n|---|---|\n| 1 | 2 |"];
        yield 'thead has bottom border' => ['thead', ['border-b-2'], "| a | b |\n|---|---|\n| 1 | 2 |"];
        yield 'th is mono uppercase' => ['th', ['font-mono', 'uppercase', 'tracking-widest'], "| a | b |\n|---|---|\n| 1 | 2 |"];
        yield 'td is padded and top-aligned' => ['td', ['px-3', 'align-top'], "| a | b |\n|---|---|\n| 1 | 2 |"];
    }

    #[DataProvider('structuralDataProvider')]
    public function testStructure(string $expectedRegex, string $markdown): void
    {
        $this->assertMatchesRegularExpression($expectedRegex, $this->convert($markdown));
    }

    /**
     * @return iterable<string, array{0: string, 1: string}>
     */
    public static function structuralDataProvider(): iterable
    {
        yield 'h2 has no class' => ['#<h2>title</h2>#', '## title'];
        yield 'h2 then h3 keeps both' => ["#<h2>title</h2>\n<h3 class=#", "## title\n### subtitle"];
        yield 'emphasis stays plain' => ['#<em>foo</em>#', '_foo_'];
        yield 'link keeps href' => ['#href="https://example\.com"#', '[foo](https://example.com)'];
        yield 'code block exposes language' => ['#<pre data-lang="php"#', "```php\necho 'hi';\n```"];
        yield 'code block is keyboard focusable' => ['#<pre[^>]*\btabindex="0"#', "```php\necho 'hi';\n```"];
        yield 'code block has aria label' => ['#<pre[^>]*\baria-label="[^"]+"#', "```php\necho 'hi';\n```"];
        yield 'table wrapped for overflow' => ['#<div class="[^"]*\boverflow-x-auto\b#', "| a | b |\n|---|---|\n| 1 | 2 |"];
        yield 'github emoji' => ['#🚀#', ':rocket:'];
    }

    public function testLevelOneHeadingThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Titles of level 1 are not accepted, please use the "title" metadata instead.');

        $this->convert('# Level 1');
    }

    public function testLevelFourHeadingThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Heading level 4 is not supported, please use level 2 or 3 only.');

        $this->convert('#### Level 4');
    }

    private function convert(string $markdown): string
    {
        return new LeagueMarkdownConverter(new TempestCodeBlockRenderer(new IdentityTranslator()))->convert($markdown);
    }
}
