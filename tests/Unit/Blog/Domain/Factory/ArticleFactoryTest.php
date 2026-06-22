<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Domain\Factory;

use App\Blog\Domain\Factory\ArticleFactory;
use App\Blog\Domain\Factory\ArticleFactory\HtmlGenerator;
use App\Blog\Domain\Model\Article;
use App\Team\Domain\Model\MemberId;
use App\Team\Infrastructure\Repository\InMemoryMemberRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class ArticleFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $yaml = Yaml::dump([
            'author' => MemberId::MathiasArlaud->value,
            'title' => 'title',
            'description' => 'description',
            'published_at' => new \DateTimeImmutable('2025-01-15'),
        ]);
        $content = sprintf("---\n%s\n---", $yaml);
        $memberRepository = new InMemoryMemberRepository([$member = aMember()->withId(MemberId::MathiasArlaud)->build()]);

        $htmlGenerator = $this->createStub(HtmlGenerator::class);
        $htmlGenerator->method('generate')->willReturn('html');

        $article = new ArticleFactory($memberRepository, $htmlGenerator)->create('1', $content);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertSame('title', $article->title);
        $this->assertSame('description', $article->description);
        $this->assertSame($member, $article->author);
        $this->assertSame('2025-01-15', $article->publishedAt->format('Y-m-d'));
        $this->assertSame('html', $article->html);
    }

    public function testCreateThrowsWhenNoMetadata(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot find metadata of file "1.md.twig".');

        new ArticleFactory(new InMemoryMemberRepository(), $this->createStub(HtmlGenerator::class))->create('1', 'anything');
    }

    public function testCreateThrowsWhenInvalidYaml(): void
    {
        $content = "---\nfoo: bar: baz\n---";

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Cannot parse metadata of file "1\.md\.twig": ".+"\.$/');

        new ArticleFactory(new InMemoryMemberRepository(), $this->createStub(HtmlGenerator::class))->create('1', $content);
    }

    public function testCreateThrowsWhenInvalidPublishedAt(): void
    {
        $yaml = Yaml::dump([
            'author' => MemberId::MathiasArlaud->value,
            'title' => 'title',
            'description' => 'description',
            'published_at' => 'not a date',
        ]);
        $content = sprintf("---\n%s\n---", $yaml);
        $memberRepository = new InMemoryMemberRepository([aMember()->withId(MemberId::MathiasArlaud)->build()]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Invalid "published_at" metadata in file "1\.md\.twig": ".+"\.$/');

        new ArticleFactory($memberRepository, $this->createStub(HtmlGenerator::class))->create('1', $content);
    }

    /**
     * @param array<string, mixed> $metadata
     */
    #[DataProvider('createThrowsWhenMissingMandatoryMetadataDataProvider')]
    public function testCreateThrowsWhenMissingMandatoryMetadata(string $expectedMissing, array $metadata): void
    {
        $yaml = Yaml::dump($metadata);
        $content = "---\n{$yaml}\n---";

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Missing "%s" metadata in file "1.md.twig".', $expectedMissing));

        new ArticleFactory(new InMemoryMemberRepository(), $this->createStub(HtmlGenerator::class))->create('1', $content);
    }

    /**
     * @return iterable<array{0: string, 1: array<string, mixed>}>
     */
    public static function createThrowsWhenMissingMandatoryMetadataDataProvider(): iterable
    {
        yield [
            'author', [
                'title' => 'title',
                'description' => 'description',
                'published_at' => new \DateTimeImmutable('2025-01-15'),
            ],
        ];
        yield [
            'title', [
                'author' => 'author',
                'description' => 'description',
                'published_at' => new \DateTimeImmutable('2025-01-15'),
            ],
        ];
        yield [
            'description', [
                'author' => 'author',
                'title' => 'title',
                'published_at' => new \DateTimeImmutable('2025-01-15'),
            ],
        ];
        yield [
            'published_at', [
                'author' => 'author',
                'title' => 'title',
                'description' => 'description',
            ],
        ];
    }
}
