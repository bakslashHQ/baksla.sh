<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Symfony\HttpKernel\CacheWarmer;

use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Blog\Domain\Repository\ArticleRepository;
use App\Blog\Infrastructure\Cache\CacheArticlePreviewRepository;
use App\Blog\Infrastructure\Cache\CacheArticleRepository;
use App\Blog\Infrastructure\Symfony\HttpKernel\CacheWarmer\ArticleCacheWarmer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Filesystem\Filesystem;

final class ArticleCacheWarmerTest extends TestCase
{
    private Filesystem $fs;

    private string $articlesDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();

        $this->articlesDir = \sprintf('%s/bakslash_test/articles', sys_get_temp_dir());

        if ($this->fs->exists($this->articlesDir)) {
            $this->fs->remove($this->articlesDir);
        }

        $this->fs->mkdir($this->articlesDir);
    }

    public function testWamup(): void
    {
        $articleRepository = $this->createStub(ArticleRepository::class);
        $articleRepository->method('get')->willReturn(anArticle()->build());

        $articlePreviewRepository = $this->createStub(ArticlePreviewRepository::class);
        $articlePreviewRepository->method('get')->willReturn(anArticlePreview()->build());
        $articlePreviewRepository->method('findShowcased')->willReturn(anArticlePreview()->build());
        $articlePreviewRepository->method('findAll')->willReturn([anArticlePreview()->build()]);
        $articlePreviewRepository->method('getHash')->willReturn('hash');

        $articleRepository = new CacheArticleRepository($articleCache = new ArrayAdapter(), $articleRepository);
        $articlePreviewRepository = new CacheArticlePreviewRepository($articlePreviewCache = new ArrayAdapter(), $articlePreviewRepository);

        $cacheWarmer = new ArticleCacheWarmer($articleRepository, $articlePreviewRepository, $this->articlesDir);

        $this->fs->touch(sprintf('%s/1.md.twig', $this->articlesDir));
        $this->fs->touch(sprintf('%s/2.md.twig', $this->articlesDir));
        $this->fs->touch(sprintf('%s/3.md', $this->articlesDir));
        $this->fs->touch(sprintf('%s/4.txt', $this->articlesDir));

        $cacheWarmer->warmUp('useless');

        $this->assertTrue($articleCache->getItem('1')->isHit());
        $this->assertTrue($articleCache->getItem('2')->isHit());
        $this->assertFalse($articleCache->getItem('3')->isHit());
        $this->assertFalse($articleCache->getItem('4')->isHit());

        $this->assertTrue($articlePreviewCache->getItem('1')->isHit());
        $this->assertTrue($articlePreviewCache->getItem('2')->isHit());
        $this->assertTrue($articlePreviewCache->getItem('_showcased')->isHit());
        $this->assertTrue($articlePreviewCache->getItem('_all')->isHit());
        $this->assertTrue($articlePreviewCache->getItem('_hash')->isHit());
        $this->assertFalse($articlePreviewCache->getItem('3')->isHit());
        $this->assertFalse($articlePreviewCache->getItem('4')->isHit());
    }
}
