<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Repository;

use App\Blog\Domain\Factory\ArticleFactory\ArticleMetadata;
use App\Blog\Domain\Factory\ArticleFactory\MetadataProvider;
use App\Team\Domain\Model\MemberId;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final readonly class YamlMetadataProvider implements MetadataProvider
{
    private const string METADATA_REGEX = '/\A---\n(?<metadata>(\n|.)+?)\n---/';

    public function __construct(
        #[Autowire(param: 'app.articles_dir')]
        private string $articlesDir,
    ) {
    }

    public function provide(string $id): ArticleMetadata
    {
        $filename = sprintf('%s.md.twig', $id);
        $path = sprintf('%s/%s', $this->articlesDir, $filename);

        if (!is_file($path)) {
            throw new \OutOfBoundsException(sprintf('No source found for article "%s".', $id));
        }

        $content = file_get_contents($path) ?: '';

        if (preg_match(self::METADATA_REGEX, $content, $matches) !== 1) {
            throw new \InvalidArgumentException(sprintf('Cannot find metadata of file "%s".', $filename));
        }

        try {
            /** @var array{author?: scalar, title?: scalar, description?: scalar, slug?: scalar, published_at?: \DateTimeInterface|string} $metadata */
            $metadata = Yaml::parse($matches['metadata'], Yaml::PARSE_DATETIME);
        } catch (ParseException $parseException) {
            throw new \InvalidArgumentException(sprintf('Cannot parse metadata of file "%s": "%s".', $filename, $parseException->getMessage()), $parseException->getCode(), previous: $parseException);
        }

        $publishedAt = $metadata['published_at'] ?? throw new \InvalidArgumentException(sprintf('Missing "published_at" metadata in file "%s".', $filename));
        if (!$publishedAt instanceof \DateTimeImmutable) {
            throw new \InvalidArgumentException(sprintf('Invalid "published_at" metadata in file "%s": "expected a date".', $filename));
        }

        return new ArticleMetadata(
            slug: (string) ($metadata['slug'] ?? throw new \InvalidArgumentException(sprintf('Missing "slug" metadata in file "%s".', $filename))),
            title: (string) ($metadata['title'] ?? throw new \InvalidArgumentException(sprintf('Missing "title" metadata in file "%s".', $filename))),
            description: (string) ($metadata['description'] ?? throw new \InvalidArgumentException(sprintf('Missing "description" metadata in file "%s".', $filename))),
            authorId: MemberId::from((string) ($metadata['author'] ?? throw new \InvalidArgumentException(sprintf('Missing "author" metadata in file "%s".', $filename)))),
            publishedAt: $publishedAt,
        );
    }
}
