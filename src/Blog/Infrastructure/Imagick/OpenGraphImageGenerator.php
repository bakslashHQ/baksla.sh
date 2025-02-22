<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Imagick;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\OpenGraph\ImageGenerator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class OpenGraphImageGenerator implements ImageGenerator
{
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
        $image->newImage(self::WIDTH, self::HEIGHT, new \ImagickPixel('#aff'));

        $backgroundImage = new \Imagick(sprintf('%s/assets/images/og-image-bg.webp', $this->projectDir));
        $image->compositeImage($backgroundImage, \Imagick::COMPOSITE_OVER, 0, 0);

        $fontPoppinsBold = new \ImagickDraw();
        $fontPoppinsBold->setFont(sprintf('%s/assets/fonts/Poppins/Poppins-Bold.woff2', $this->projectDir));
        $fontPoppinsBold->setFillColor(new \ImagickPixel('#1e293b'));

        $fontPoppinsSemiBold = new \ImagickDraw();
        $fontPoppinsSemiBold->setFont(sprintf('%s/assets/fonts/Poppins/Poppins-SemiBold.woff2', $this->projectDir));
        $fontPoppinsSemiBold->setFillColor(new \ImagickPixel('#1e293b'));

        // Draw title
        $titleFont = clone $fontPoppinsBold;
        $titleFont->setFontSize(self::TITLE_FONT_SIZE);
        $titleFont->setTextKerning(-3.0);
        $titleFont->setTextAlignment(\Imagick::ALIGN_CENTER);

        $titleLines = $this->wrapText($article->title, self::WIDTH - 2 * self::SPACING, $titleFont);
        $titleLineHeight = 1.15;
        $titleY = self::HEIGHT / 2 + (3 * self::SPACING);
        foreach ($titleLines as $i => $line) {
            $lineY = (int) ($titleY - ($titleFont->getFontSize() * $titleLineHeight) * (count($titleLines) - $i));
            $image->annotateImage($titleFont, self::WIDTH / 2, $lineY, 0, $line);
        }

        // Draw author
        $authorFont = clone $fontPoppinsSemiBold;
        $authorFont->setFontSize(self::AUTHOR_FONT_SIZE);

        $authorImage = new \Imagick(sprintf('%s/assets/images/blog/author/%s', $this->projectDir, $article->author->picture));
        $authorImage->resizeImage(self::AUTHOR_AVATAR_SIZE, self::AUTHOR_AVATAR_SIZE, \Imagick::FILTER_LANCZOS, 1);
        $authorImage->roundCornersImage($authorImage->getImageWidth(), $authorImage->getImageHeight());

        $authorNameMetrics = $image->queryFontMetrics($authorFont, $article->author->name);
        $authorWidth = $authorNameMetrics['textWidth'] + self::SPACING + $authorImage->getImageWidth();
        $authorHeight = $authorNameMetrics['textHeight'];

        $authorX = (int) ((self::WIDTH - $authorWidth) / 2);
        $authorY = $titleY + self::SPACING;

        $image->annotateImage($authorFont, $authorX + $authorImage->getImageWidth() + self::SPACING, (int) ($authorY + ($authorHeight / 2) - abs($authorNameMetrics['descender'])), 0, $article->author->name);
        $image->compositeImage($authorImage, \Imagick::COMPOSITE_OVER, $authorX, (int) ($authorY - ($authorImage->getImageHeight() / 2)));

        // Draw logo
        $logoImage = new \Imagick(sprintf('%s/assets/images/bakslash.png', $this->projectDir));
        $logoImage->resizeImage((int) ($logoImage->getImageWidth() * self::LOGO_HEIGHT / $logoImage->getImageHeight()), self::LOGO_HEIGHT, \Imagick::FILTER_LANCZOS, 1);

        $image->compositeImage($logoImage, \Imagick::COMPOSITE_OVER, (int) ((self::WIDTH - $logoImage->getImageWidth()) / 2), self::HEIGHT - self::SPACING - $logoImage->getImageHeight());

        // Returns the image
        $image->setFormat('jpeg');
        $image->setImageCompressionQuality(100);

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
}
