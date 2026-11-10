<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure;

/**
 * Naming of the article sources: one "<id>.<locale>.md.twig" file per article and locale.
 */
final class ArticleSource
{
    public static function filename(string $id, string $locale): string
    {
        return sprintf('%s.%s.md.twig', $id, $locale);
    }

    public static function template(string $id, string $locale): string
    {
        return sprintf('articles/%s', self::filename($id, $locale));
    }

    public static function glob(string $locale): string
    {
        return self::filename('*', $locale);
    }

    public static function id(string $filename, string $locale): string
    {
        return basename($filename, self::filename('', $locale));
    }
}
