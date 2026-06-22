<?php

declare(strict_types=1);

namespace App\Blog\Domain\Factory;

use App\Blog\Domain\Factory\ArticleFactory\HtmlGenerator;
use App\Blog\Domain\Model\Article;
use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Repository\MemberRepository;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final readonly class ArticleFactory
{
    private const string METADATA_REGEX = '/\A---\n(?<metadata>(\n|.)+?)\n---/';

    public function __construct(
        private MemberRepository $memberRepository,
        private HtmlGenerator $htmlGenerator,
    ) {
    }

    public function create(string $id, string $content): Article
    {
        $filename = sprintf('%s.md.twig', $id);

        if (preg_match(self::METADATA_REGEX, $content, $matches) !== 1) {
            throw new \InvalidArgumentException(sprintf('Cannot find metadata of file "%s".', $filename));
        }

        try {
            /** @var array{author?: string, title?: string, description?: string, published_at?: \DateTimeInterface|string} $metadata */
            $metadata = Yaml::parse($matches['metadata'], Yaml::PARSE_DATETIME);
        } catch (ParseException $parseException) {
            throw new \InvalidArgumentException(sprintf('Cannot parse metadata of file "%s": "%s".', $filename, $parseException->getMessage()), $parseException->getCode(), previous: $parseException);
        }

        $authorId = $metadata['author'] ?? throw new \InvalidArgumentException(sprintf('Missing "author" metadata in file "%s".', $filename));

        $publishedAt = $metadata['published_at'] ?? throw new \InvalidArgumentException(sprintf('Missing "published_at" metadata in file "%s".', $filename));
        if (!$publishedAt instanceof \DateTimeImmutable) {
            throw new \InvalidArgumentException(sprintf('Invalid "published_at" metadata in file "%s": "expected a date".', $filename));
        }

        return new Article(
            id: $id,
            title: $metadata['title'] ?? throw new \InvalidArgumentException(sprintf('Missing "title" metadata in file "%s".', $filename)),
            description: $metadata['description'] ?? throw new \InvalidArgumentException(sprintf('Missing "description" metadata in file "%s".', $filename)),
            html: $this->htmlGenerator->generate($id),
            author: $this->memberRepository->get(MemberId::from($authorId)),
            publishedAt: $publishedAt,
        );
    }
}
