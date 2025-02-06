<?php

declare(strict_types=1);

namespace App\Blog\Domain\Factory;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Model\Author;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final readonly class ArticlePreviewFactory
{
    private const string METADATA_REGEX = '/\A---\n(?<metadata>(\n|.)+?)\n---/';

    public function create(string $id, string $content): ArticlePreview
    {
        $filename = sprintf('%s.md.twig', $id);

        if (in_array(preg_match(self::METADATA_REGEX, $content, $matches), [0, false], true)) {
            throw new \InvalidArgumentException(sprintf('Cannot find metadata of file "%s".', $filename));
        }

        try {
            /** @var array{author?: string, authorPicture?: string, authorBsky?: string, title?: string, description?: string} $metadata */
            $metadata = Yaml::parse($matches['metadata']);
        } catch (ParseException $parseException) {
            throw new \InvalidArgumentException(sprintf('Cannot parse metadata of file "%s": "%s".', $filename, $parseException->getMessage()), $parseException->getCode(), previous: $parseException);
        }

        $author = new Author(
            name: $metadata['author'] ?? throw new \InvalidArgumentException(sprintf('Missing "author" metadata in file "%s".', $filename)),
            picture: $metadata['authorPicture'] ?? throw new \InvalidArgumentException(sprintf('Missing "authorPicture" metadata in file "%s".', $filename)),
            bsky: $metadata['authorBsky'] ?? null,
        );

        return new ArticlePreview(
            id: $id,
            title: $metadata['title'] ?? throw new \InvalidArgumentException(sprintf('Missing "title" metadata in file "%s".', $filename)),
            description: $metadata['description'] ?? throw new \InvalidArgumentException(sprintf('Missing "description" metadata in file "%s".', $filename)),
            author: $author,
        );
    }
}
