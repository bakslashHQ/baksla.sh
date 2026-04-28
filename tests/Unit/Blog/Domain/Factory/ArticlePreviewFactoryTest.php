<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Domain\Factory;

use App\Blog\Domain\Factory\ArticlePreviewFactory;
use App\Blog\Domain\Model\ArticlePreview;
use App\Team\Domain\Model\MemberId;
use App\Team\Infrastructure\Repository\InMemoryMemberRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ArticlePreviewFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $author = MemberId::MathiasArlaud->value;
        $content = <<<MD
            ---
            author: {$author}
            title: title
            description: description
            publishedAt: 2025-04-01
            ---
            MD;
        $memberRepository = new InMemoryMemberRepository([$member = aMember()->withId(MemberId::MathiasArlaud)->build()]);

        $preview = new ArticlePreviewFactory($memberRepository)->create('1', $content);

        $this->assertInstanceOf(ArticlePreview::class, $preview);
        $this->assertSame('title', $preview->title);
        $this->assertSame('description', $preview->description);
        $this->assertEquals(new \DateTimeImmutable('2025-04-01'), $preview->publishedAt);
        $this->assertSame($member, $preview->author);
    }

    public function testCreateThrowsWhenInvalidPublishedAt(): void
    {
        $author = MemberId::MathiasArlaud->value;
        $content = <<<MD
            ---
            author: {$author}
            title: title
            description: description
            publishedAt: not-a-date
            ---
            MD;
        $memberRepository = new InMemoryMemberRepository([aMember()->withId(MemberId::MathiasArlaud)->build()]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid "publishedAt" metadata in file "1.md.twig": expected a date.');

        new ArticlePreviewFactory($memberRepository)->create('1', $content);
    }

    public function testCreateThrowsWhenNoMetadata(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot find metadata of file "1.md.twig".');

        new ArticlePreviewFactory(new InMemoryMemberRepository())->create('1', 'anything');
    }

    public function testCreateThrowsWhenInvalidYaml(): void
    {
        $content = "---\nfoo: bar: baz\n---";

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Cannot parse metadata of file "1\.md\.twig": ".+"\.$/');

        new ArticlePreviewFactory(new InMemoryMemberRepository())->create('1', $content);
    }

    #[DataProvider('createThrowsWhenMissingMandatoryMetadataDataProvider')]
    public function testCreateThrowsWhenMissingMandatoryMetadata(string $expectedMissing, string $yaml): void
    {
        $content = "---\n{$yaml}\n---";

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Missing "%s" metadata in file "1.md.twig".', $expectedMissing));

        new ArticlePreviewFactory(new InMemoryMemberRepository())->create('1', $content);
    }

    /**
     * @return iterable<array{0: string, 1: string}>
     */
    public static function createThrowsWhenMissingMandatoryMetadataDataProvider(): iterable
    {
        yield ['author', "title: title\ndescription: description\npublishedAt: 2025-04-01"];
        yield ['title', "author: author\ndescription: description\npublishedAt: 2025-04-01"];
        yield ['description', "author: author\ntitle: title\npublishedAt: 2025-04-01"];
        yield ['publishedAt', "author: author\ntitle: title\ndescription: description"];
    }
}
