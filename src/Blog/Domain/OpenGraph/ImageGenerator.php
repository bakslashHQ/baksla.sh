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

    public const int SPACING = 24;

    public const int AUTHOR_AVATAR_SIZE = 64;

    public const int TITLE_FONT_SIZE = 80;

    public const int AUTHOR_FONT_SIZE = 34;

    public const int LOGO_HEIGHT = 32;

    /**
     * @return string The image content in JPEG format.
     */
    public function generate(Article $article): string;
}
