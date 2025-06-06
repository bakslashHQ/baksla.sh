<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Repository;

use App\Blog\Domain\Repository\ArticleRepository;
use App\Blog\Infrastructure\Repository\CachingArticleRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class CacheArticleRepositoryTest extends TestCase
{
    public function testGetIsCached(): void
    {
        $article = anArticle()->build();

        $decorated = $this->createMock(ArticleRepository::class);
        $decorated->expects($this->once())->method('get')->willReturn($article);

        $repository = new CachingArticleRepository(new ArrayAdapter(), $decorated);

        $this->assertEquals($article, $repository->get('1'));
        $this->assertEquals($article, $repository->get('1'));
    }

    public function testFindAllIsCached(): void
    {
        $decorated = $this->createMock(ArticleRepository::class);
        $decorated->expects($this->once())->method('findAll')->willReturn([anArticle()->build()]);

        $repository = new CachingArticleRepository(new ArrayAdapter(), $decorated);

        $this->assertCount(1, $repository->findAll());
        $this->assertCount(1, $repository->findAll());
    }
}
