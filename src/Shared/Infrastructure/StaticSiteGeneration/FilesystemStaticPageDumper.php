<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\StaticSiteGeneration;

use Symfony\Component\Filesystem\Filesystem;

final readonly class FilesystemStaticPageDumper implements StaticPageDumperInterface
{
    private Filesystem $filesystem;

    public function __construct(
        private string $outputDir,
    ) {
        $this->filesystem = new Filesystem();
    }

    public function dump(string $uri, string $content, ?string $format = null): void
    {
        if ($uri === '/') {
            $fileName = 'index.html';
        } elseif ($format && !str_ends_with($uri, '.' . $format)) {
            $fileName = \sprintf('%s.%s', $uri, $format);
        } else {
            $fileName = $uri;
        }

        if ($format === 'html' || str_ends_with($fileName, '.html')) {
            $content = $this->minifyHtml($content);
        }

        $this->filesystem->dumpFile(\sprintf('%s/%s', $this->outputDir, $fileName), $content);
    }

    private function minifyHtml(string $html): string
    {
        // Preserve preformatted blocks (<pre>, <code>, <script>, <textarea>)
        $preserved = [];
        $html = preg_replace_callback('/<(pre|code|script|textarea)\b[^>]*>.*?<\/\1>/si', static function (array $match) use (&$preserved): string {
            $placeholder = '<!--PRESERVE:' . \count($preserved) . '-->';
            $preserved[] = $match[0];

            return $placeholder;
        }, $html) ?? $html;

        // Remove HTML comments (except conditional comments and placeholders)
        $html = preg_replace('/<!--(?!\[if|PRESERVE:).*?-->/s', '', $html) ?? $html;

        // Collapse whitespace between tags
        $html = preg_replace('/>\s+</', '> <', $html) ?? $html;

        // Trim lines
        $html = preg_replace('/^\s+/m', '', $html) ?? $html;

        // Restore preserved blocks
        foreach ($preserved as $i => $block) {
            $html = str_replace('<!--PRESERVE:' . $i . '-->', $block, $html);
        }

        return trim($html);
    }
}
