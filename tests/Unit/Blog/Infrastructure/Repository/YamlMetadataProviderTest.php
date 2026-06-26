<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Repository;

use App\Blog\Domain\Factory\ArticleFactory\ArticleMetadata;
use App\Blog\Infrastructure\Repository\YamlMetadataProvider;
use App\Team\Domain\Model\MemberId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final class YamlMetadataProviderTest extends TestCase
{
    private Filesystem $fs;

    private string $articlesDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();
        $this->articlesDir = sprintf('%s/bakslash_test/articles', sys_get_temp_dir());

        if ($this->fs->exists($this->articlesDir)) {
            $this->fs->remove($this->articlesDir);
        }

        $this->fs->mkdir($this->articlesDir);
    }

    public function testProvide(): void
    {
        $yaml = Yaml::dump([
            'author' => MemberId::MathiasArlaud->value,
            'title' => 'title',
            'description' => 'description',
            'slug' => 'a-slug',
            'published_at' => new \DateTimeImmutable('2025-01-15'),
        ]);

        $metadata = $this->provide(sprintf("---\n%s\n---", $yaml));

        $this->assertInstanceOf(ArticleMetadata::class, $metadata);
        $this->assertSame('a-slug', $metadata->slug);
        $this->assertSame('title', $metadata->title);
        $this->assertSame('description', $metadata->description);
        $this->assertSame(MemberId::MathiasArlaud, $metadata->authorId);
        $this->assertSame('2025-01-15', $metadata->publishedAt->format('Y-m-d'));
    }

    public function testThrowsWhenSourceMissing(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessageIs('No source found for article "missing".');

        new YamlMetadataProvider($this->articlesDir)->provide('missing');
    }

    public function testThrowsWhenNoMetadata(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot find metadata of file "1.md.twig".');

        $this->provide('anything');
    }

    public function testThrowsWhenInvalidYaml(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Cannot parse metadata of file "1\.md\.twig": ".+"\.$/');

        $this->provide("---\nfoo: bar: baz\n---");
    }

    public function testThrowsWhenInvalidPublishedAt(): void
    {
        $yaml = Yaml::dump([
            'author' => MemberId::MathiasArlaud->value,
            'title' => 'title',
            'description' => 'description',
            'slug' => 'a-slug',
            'published_at' => 'not a date',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Invalid "published_at" metadata in file "1\.md\.twig": ".+"\.$/');

        $this->provide(sprintf("---\n%s\n---", $yaml));
    }

    /**
     * @param array<string, mixed> $metadata
     */
    #[DataProvider('throwsWhenMissingMandatoryMetadataDataProvider')]
    public function testThrowsWhenMissingMandatoryMetadata(string $expectedMissing, array $metadata): void
    {
        $yaml = Yaml::dump($metadata);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Missing "%s" metadata in file "1.md.twig".', $expectedMissing));

        $this->provide("---\n{$yaml}\n---");
    }

    /**
     * @return iterable<array{0: string, 1: array<string, mixed>}>
     */
    public static function throwsWhenMissingMandatoryMetadataDataProvider(): iterable
    {
        yield [
            'author', [
                'slug' => 'a-slug',
                'title' => 'title',
                'description' => 'description',
                'published_at' => new \DateTimeImmutable('2025-01-15'),
            ],
        ];
        yield [
            'slug', [
                'author' => MemberId::MathiasArlaud->value,
                'title' => 'title',
                'description' => 'description',
                'published_at' => new \DateTimeImmutable('2025-01-15'),
            ],
        ];
        yield [
            'title', [
                'author' => MemberId::MathiasArlaud->value,
                'slug' => 'a-slug',
                'description' => 'description',
                'published_at' => new \DateTimeImmutable('2025-01-15'),
            ],
        ];
        yield [
            'description', [
                'author' => MemberId::MathiasArlaud->value,
                'slug' => 'a-slug',
                'title' => 'title',
                'published_at' => new \DateTimeImmutable('2025-01-15'),
            ],
        ];
        yield [
            'published_at', [
                'author' => MemberId::MathiasArlaud->value,
                'slug' => 'a-slug',
                'title' => 'title',
                'description' => 'description',
            ],
        ];
    }

    private function provide(string $content): ArticleMetadata
    {
        $this->fs->dumpFile(sprintf('%s/1.md.twig', $this->articlesDir), $content);

        return new YamlMetadataProvider($this->articlesDir)->provide('1');
    }
}
