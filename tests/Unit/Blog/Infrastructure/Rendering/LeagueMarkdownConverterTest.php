<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\League\CommonMark;

use App\Blog\Infrastructure\Rendering\CodeBlock\TempestCodeBlockRenderer;
use App\Blog\Infrastructure\Rendering\LeagueMarkdownConverter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LeagueMarkdownConverterTest extends TestCase
{
    #[DataProvider('convertDataProvider')]
    public function testConvert(string $expectedHtml, string $markdown): void
    {
        $converter = new LeagueMarkdownConverter(new TempestCodeBlockRenderer());

        $this->assertMatchesRegularExpression($expectedHtml, $converter->convert($markdown));
    }

    /**
     * @return iterable<array{0: string, 1: string}>
     */
    public static function convertDataProvider(): iterable
    {
        yield ['#<p class="mb-6">foo</p>#', 'foo'];
        yield ['#<p>foo</p>#', "foo\n## title"];
        yield ['#<h2 class=".*border-b border-indigo-500/50 border-b-8.*">title</h2>#', '## title'];
        yield ['#<h3 class=".*(?!border-b border-indigo-500/50 border-b-8).*">title</h3>#', '### title'];
        yield ['#<h2 class=".*text-3xl.*">title</h2>#', '## title'];
        yield ['#<h3 class=".*text-2xl.*">title</h3>#', '### title'];
        yield ['#<h2 class=".*mb-8 mt-10 sm:mt-20.*">title</h2>#', '## title'];
        yield ['#<h3 class=".*mb-4 mt-10.*">title</h3>#', '### title'];
        yield ["#<h2 class=\".*mt-10 sm:mt-20.*\">title</h2>\n<h3 class=\".*mb-4.*\">subtitle</h3>#", "## title\n### subtitle"];
        yield ['#<ul class=".*list-disc.*">#', '- foo'];
        yield ['#<ol class=".*list-decimal.*">#', '1. foo'];
        yield ['#<ul class=".*(?!list-).*">#', '* foo'];
        yield ['#<ul class=".*mb-6.*">#', '- foo'];
        yield ['#<ul class=".*mb-3.*">#', "- foo\n1. bar"];
        yield ['#<ul class=".*pl-12.*">#', '- foo'];
        yield ['#<ol class=".*pl-8.*">#', "- foo\n1. bar"];
        yield ['#<div class="code"><pre data-lang="php" class="notranslate">#', "```php\necho 'hi';\n```"];
        yield ['#🚀#', ':rocket:'];
    }

    public function testLevelOneHeadingThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Titles of level 1 are not accepted, please use the "title" metadata instead.');

        (new LeagueMarkdownConverter(new TempestCodeBlockRenderer()))->convert('# Level 1');
    }
}
