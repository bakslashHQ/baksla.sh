<?php

declare(strict_types=1);

namespace App\Blog\Domain\OpenGraph;

use App\Blog\Domain\Model\Article;

/**
 * Generate an OpenGraph image for a given Article.
 */
interface ImageGenerator
{
    public const int WIDTH = 1200;

    public const int HEIGHT = 630;

    public const int MARGIN = 64;

    public const int AUTHOR_AVATAR_SIZE = 72;

    public const int LOGO_SIZE = 56;

    public const int CAPTION_FONT_SIZE = 24;

    public const int TITLE_FONT_SIZE = 76;

    public const int META_FONT_SIZE = 22;

    public const int AUTHOR_FONT_SIZE = 30;

    public const int WORDMARK_FONT_SIZE = 44;

    /**
     * @return string The image content in JPEG format.
     */
    public function generate(Article $article): string;
}
