<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Domain\Factory;

use App\Blog\Domain\Factory\ArticleFactory;
use App\Blog\Domain\Factory\ArticleFactory\ArticleMetadata;
use App\Blog\Domain\Factory\ArticleFactory\HtmlProvider;
use App\Blog\Domain\Factory\ArticleFactory\MetadataProvider;
use App\Blog\Domain\Model\Article;
use App\Team\Domain\Model\MemberId;
use App\Team\Infrastructure\Repository\InMemoryMemberRepository;
use PHPUnit\Framework\TestCase;

final class ArticleFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $memberRepository = new InMemoryMemberRepository([$member = aMember()->withId(MemberId::MathiasArlaud)->build()]);

        $metadataProvider = $this->createStub(MetadataProvider::class);
        $metadataProvider->method('provide')->willReturn(new ArticleMetadata(
            slug: 'a-slug',
            title: 'title',
            description: 'description',
            authorId: MemberId::MathiasArlaud,
            publishedAt: new \DateTimeImmutable('2025-01-15'),
        ));

        $htmlProvider = $this->createStub(HtmlProvider::class);
        $htmlProvider->method('provide')->willReturn('html');

        $article = new ArticleFactory($memberRepository, $metadataProvider, $htmlProvider)->create('1', 'en');

        $this->assertInstanceOf(Article::class, $article);
        $this->assertSame('1', $article->id);
        $this->assertSame('a-slug', $article->slug);
        $this->assertSame('title', $article->title);
        $this->assertSame('description', $article->description);
        $this->assertSame($member, $article->author);
        $this->assertSame('2025-01-15', $article->publishedAt->format('Y-m-d'));
        $this->assertSame('html', $article->html);
    }

    public function testHtmlIsRenderedLazily(): void
    {
        $memberRepository = new InMemoryMemberRepository([aMember()->withId(MemberId::MathiasArlaud)->build()]);

        $metadataProvider = $this->createStub(MetadataProvider::class);
        $metadataProvider->method('provide')->willReturn(new ArticleMetadata(
            slug: 'a-slug',
            title: 'title',
            description: 'description',
            authorId: MemberId::MathiasArlaud,
            publishedAt: new \DateTimeImmutable('2025-01-15'),
        ));

        $renders = 0;
        $htmlProvider = $this->createStub(HtmlProvider::class);
        $htmlProvider->method('provide')->willReturnCallback(static function () use (&$renders): string {
            ++$renders;

            return 'html';
        });

        $article = new ArticleFactory($memberRepository, $metadataProvider, $htmlProvider)->create('1', 'en');

        $this->assertSame('a-slug', $article->slug);
        $this->assertSame(0, $renders);

        $this->assertSame('html', $article->html);
        $this->assertSame(1, $renders);
    }

    public function testForwardsTheLocaleToTheProviders(): void
    {
        $memberRepository = new InMemoryMemberRepository([aMember()->withId(MemberId::MathiasArlaud)->build()]);

        $metadataProvider = $this->createMock(MetadataProvider::class);
        $metadataProvider->expects($this->once())->method('provide')->with('1', 'fr')->willReturn(new ArticleMetadata(
            slug: 'un-slug',
            title: 'titre',
            description: 'description',
            authorId: MemberId::MathiasArlaud,
            publishedAt: new \DateTimeImmutable('2025-01-15'),
        ));

        $htmlProvider = $this->createMock(HtmlProvider::class);
        $htmlProvider->expects($this->once())->method('provide')->with('1', 'fr')->willReturn('html');

        $this->assertSame('html', new ArticleFactory($memberRepository, $metadataProvider, $htmlProvider)->create('1', 'fr')->html);
    }
}
