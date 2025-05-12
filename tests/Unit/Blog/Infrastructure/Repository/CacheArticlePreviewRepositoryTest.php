<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Repository;

use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Blog\Infrastructure\Repository\CachingArticlePreviewRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class CacheArticlePreviewRepositoryTest extends TestCase
{
    public function testGetIsCached(): void
    {
        $preview = anArticlePreview()->build();

        $decorated = $this->createMock(ArticlePreviewRepository::class);
        $decorated->expects($this->once())->method('get')->willReturn($preview);

        $repository = new CachingArticlePreviewRepository(new ArrayAdapter(), $decorated);

        $this->assertEquals($preview, $repository->get('1'));
        $this->assertEquals($preview, $repository->get('1'));
    }

    public function testFindShowcasedIsCached(): void
    {
        $preview = anArticlePreview()->build();

        $decorated = $this->createMock(ArticlePreviewRepository::class);
        $decorated->expects($this->once())->method('findShowcased')->willReturn($preview);

        $repository = new CachingArticlePreviewRepository(new ArrayAdapter(), $decorated);

        $this->assertEquals($preview, $repository->findShowcased());
        $this->assertEquals($preview, $repository->findShowcased());
    }

    public function testFindAllIsCached(): void
    {
        $decorated = $this->createMock(ArticlePreviewRepository::class);
        $decorated->expects($this->once())->method('findAll')->willReturn([anArticlePreview()->build()]);

        $repository = new CachingArticlePreviewRepository(new ArrayAdapter(), $decorated);

        $this->assertCount(1, $repository->findAll());
        $this->assertCount(1, $repository->findAll());
    }

    public function testGetHashIsCached(): void
    {
        $hash = 'hash';

        $decorated = $this->createMock(ArticlePreviewRepository::class);
        $decorated->expects($this->once())->method('getHash')->willReturn($hash);

        $repository = new CachingArticlePreviewRepository(new ArrayAdapter(), $decorated);

        $this->assertSame($hash, $repository->getHash());
        $this->assertSame($hash, $repository->getHash());
    }
}
