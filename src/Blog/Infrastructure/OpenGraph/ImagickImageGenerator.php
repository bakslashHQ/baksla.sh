<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\OpenGraph;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\OpenGraph\ImageGenerator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class ImagickImageGenerator implements ImageGenerator
{
    private const string COLOR_PAPER = '#f6f2e8';

    private const string COLOR_INK = '#1f2130';

    private const string COLOR_MUTED = '#5e646c';

    private const string COLOR_ACCENT = '#860dff';

    public function __construct(
        #[Autowire(param: 'kernel.project_dir')]
        private string $projectDir,
    ) {
        if (!class_exists(\Imagick::class)) {
            throw new \RuntimeException('Imagick is not installed');
        }
    }

    public function generate(Article $article): string
    {
        $image = new \Imagick();
        $image->newImage(self::WIDTH, self::HEIGHT, new \ImagickPixel(self::COLOR_PAPER));

        $spaceGroteskBold = $this->font('SpaceGrotesk/SpaceGrotesk-Bold.woff2');
        $jetBrainsMono = $this->font('JetBrainsMono/JetBrainsMono-Variable.woff2');

        // Title: Space Grotesk Bold, ink
        $titleDraw = new \ImagickDraw();
        $titleDraw->setFont($spaceGroteskBold);
        $titleDraw->setFontSize(self::TITLE_FONT_SIZE);
        $titleDraw->setTextKerning(-2.0);
        $titleDraw->setFillColor(new \ImagickPixel(self::COLOR_INK));
        $titleDraw->setTextAlignment(\Imagick::ALIGN_LEFT);

        $titleLineSpacing = (int) (self::TITLE_FONT_SIZE * 1.05);
        $titleLines = $this->wrapText($article->title, self::WIDTH - 2 * self::MARGIN, $titleDraw);
        $lastIndex = \count($titleLines) - 1;

        $captionTitleGap = 88;
        $titleMetaGap = 48;

        // Vertically center the caption / title / meta block in the area above the footer row.
        // Realistic article titles fit in 1-3 lines; very long titles may approach the footer.
        $footerTop = self::HEIGHT - self::MARGIN - self::AUTHOR_AVATAR_SIZE;
        $blockHeight = self::CAPTION_FONT_SIZE + $captionTitleGap + $lastIndex * $titleLineSpacing + $titleMetaGap;
        $blockTop = max(self::MARGIN, (int) (($footerTop - $blockHeight) / 2));

        // Caption: `# BLOG.md`
        $captionDraw = new \ImagickDraw();
        $captionDraw->setFont($jetBrainsMono);
        $captionDraw->setFontSize(self::CAPTION_FONT_SIZE);
        $captionDraw->setFillColor(new \ImagickPixel(self::COLOR_ACCENT));
        $captionDraw->setTextAlignment(\Imagick::ALIGN_LEFT);

        $captionBaselineY = $blockTop + self::CAPTION_FONT_SIZE;
        $image->annotateImage($captionDraw, self::MARGIN, $captionBaselineY, 0, '# BLOG.md');

        $titleBaselineY = $captionBaselineY + $captionTitleGap;
        foreach ($titleLines as $i => $line) {
            $image->annotateImage($titleDraw, self::MARGIN, $titleBaselineY + $i * $titleLineSpacing, 0, $line);
        }
        $lastTitleBaselineY = $titleBaselineY + $lastIndex * $titleLineSpacing;

        // Meta: date · reading time, monospace, muted
        $metaDraw = new \ImagickDraw();
        $metaDraw->setFont($jetBrainsMono);
        $metaDraw->setFontSize(self::META_FONT_SIZE);
        $metaDraw->setFillColor(new \ImagickPixel(self::COLOR_MUTED));
        $metaDraw->setTextAlignment(\Imagick::ALIGN_LEFT);

        $meta = sprintf('%s  ·  %d min read', strtoupper($article->publishedAt->format('M j, Y')), $article->readingTime);
        $image->annotateImage($metaDraw, self::MARGIN, $lastTitleBaselineY + $titleMetaGap, 0, $meta);

        // Bottom row: avatar + author name (left), `baksla.sh` wordmark (right)
        $avatarTop = self::HEIGHT - self::MARGIN - self::AUTHOR_AVATAR_SIZE;

        $avatar = new \Imagick(sprintf('%s/assets/images/team/members/%s.webp', $this->projectDir, $article->author->id->value));
        $avatar->resizeImage(self::AUTHOR_AVATAR_SIZE, self::AUTHOR_AVATAR_SIZE, \Imagick::FILTER_LANCZOS, 1);
        $avatar->roundCornersImage($avatar->getImageWidth(), $avatar->getImageHeight());

        $image->compositeImage($avatar, \Imagick::COMPOSITE_OVER, self::MARGIN, $avatarTop);

        $authorDraw = new \ImagickDraw();
        $authorDraw->setFont($spaceGroteskBold);
        $authorDraw->setFontSize(self::AUTHOR_FONT_SIZE);
        $authorDraw->setFillColor(new \ImagickPixel(self::COLOR_INK));
        $authorDraw->setTextAlignment(\Imagick::ALIGN_LEFT);

        $authorMetrics = $image->queryFontMetrics($authorDraw, $article->author->getFullname());
        $authorBaselineY = $avatarTop + (int) ((self::AUTHOR_AVATAR_SIZE + $authorMetrics['textHeight']) / 2 - abs($authorMetrics['descender']));
        $image->annotateImage($authorDraw, self::MARGIN + self::AUTHOR_AVATAR_SIZE + 20, $authorBaselineY, 0, $article->author->getFullname());

        // Wordmark: circle logo + `baksla.sh` with accent period
        $wordmarkDraw = new \ImagickDraw();
        $wordmarkDraw->setFont($spaceGroteskBold);
        $wordmarkDraw->setFontSize(self::WORDMARK_FONT_SIZE);
        $wordmarkDraw->setTextKerning(-1.0);
        $wordmarkDraw->setFillColor(new \ImagickPixel(self::COLOR_INK));
        $wordmarkDraw->setTextAlignment(\Imagick::ALIGN_LEFT);

        $headMetrics = $image->queryFontMetrics($wordmarkDraw, 'baksla');
        $dotMetrics = $image->queryFontMetrics($wordmarkDraw, '.');
        $tailMetrics = $image->queryFontMetrics($wordmarkDraw, 'sh');
        $textWidth = (int) ($headMetrics['textWidth'] + $dotMetrics['textWidth'] + $tailMetrics['textWidth']);

        $svgDpi = 300;
        $logo = new \Imagick();
        $logo->setBackgroundColor(new \ImagickPixel('transparent'));
        $logo->setResolution($svgDpi, $svgDpi);
        $logo->readImage(sprintf('%s/assets/images/bakslash-small.svg', $this->projectDir));
        $logo->setImageFormat('png');
        $logo->resizeImage(self::LOGO_SIZE, self::LOGO_SIZE, \Imagick::FILTER_LANCZOS, 1);

        $logoTextGap = 16;
        $wordmarkWidth = self::LOGO_SIZE + $logoTextGap + $textWidth;
        $wordmarkX = self::WIDTH - self::MARGIN - $wordmarkWidth;
        $wordmarkBaselineY = $avatarTop + (int) ((self::AUTHOR_AVATAR_SIZE + $headMetrics['textHeight']) / 2 - abs($headMetrics['descender']));

        $image->compositeImage($logo, \Imagick::COMPOSITE_OVER, $wordmarkX, $avatarTop + (self::AUTHOR_AVATAR_SIZE - self::LOGO_SIZE) / 2);

        $textX = $wordmarkX + self::LOGO_SIZE + $logoTextGap;
        $image->annotateImage($wordmarkDraw, $textX, $wordmarkBaselineY, 0, 'baksla');
        $wordmarkDraw->setFillColor(new \ImagickPixel(self::COLOR_ACCENT));
        $image->annotateImage($wordmarkDraw, $textX + (int) $headMetrics['textWidth'], $wordmarkBaselineY, 0, '.');
        $wordmarkDraw->setFillColor(new \ImagickPixel(self::COLOR_INK));
        $image->annotateImage($wordmarkDraw, $textX + (int) ($headMetrics['textWidth'] + $dotMetrics['textWidth']), $wordmarkBaselineY, 0, 'sh');

        $image->setFormat('jpeg');
        $image->setImageCompressionQuality(88);

        return (string) $image;
    }

    /**
     * Wrap text to fit within a given width and font.
     *
     * @see https://github.com/Intervention/image/issues/143#issuecomment-492592752
     * @see https://github.com/Intervention/image/issues/143#issuecomment-1907689756
     *
     * @return list<string>
     */
    private function wrapText(string $text, int $width, \ImagickDraw $font): array
    {
        $imagick = new \Imagick();

        $line = [];
        $lines = [];

        foreach (explode(' ', $text) as $word) {
            $line[] = $word;

            $fontMetrics = $imagick->queryFontMetrics($font, implode(' ', $line));

            // If our line doesn't fit, remove the last word and place it on a new line
            if ($fontMetrics['textWidth'] >= $width) {
                array_pop($line);
                $lines[] = implode(' ', $line);
                $line = [$word];
            }
        }

        $lines[] = implode(' ', $line);

        return $lines;
    }

    private function font(string $relativePath): string
    {
        return sprintf('%s/assets/fonts/%s', $this->projectDir, $relativePath);
    }
}
