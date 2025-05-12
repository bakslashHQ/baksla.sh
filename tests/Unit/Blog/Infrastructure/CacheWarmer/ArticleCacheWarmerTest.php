<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\CacheWarmer;

use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Blog\Domain\Repository\ArticleRepository;
use App\Blog\Infrastructure\CacheWarmer\ArticleCacheWarmer;
use App\Blog\Infrastructure\Repository\CachingArticlePreviewRepository;
use App\Blog\Infrastructure\Repository\CachingArticleRepository;
use App\Tests\Fake\FakeBlogOpenGraphImageGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Filesystem\Filesystem;

final class ArticleCacheWarmerTest extends TestCase
{
    private Filesystem $fs;

    private string $tmpDir;

    private string $articlesDir;

    private string $publicDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();

        $this->tmpDir = $this->fs->tempnam(sys_get_temp_dir(), '');
        $this->fs->remove($this->tmpDir);
        $this->fs->mkdir($this->tmpDir);

        $this->articlesDir = \sprintf('%s/articles', $this->tmpDir);
        $this->fs->mkdir($this->articlesDir);

        $this->publicDir = \sprintf('%s/public', $this->tmpDir);
        $this->fs->mkdir($this->publicDir);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->fs->remove($this->tmpDir);
    }

    public function testWarmup(): void
    {
        $articleRepository = $this->createStub(ArticleRepository::class);
        $articleRepository->method('get')->willReturn(anArticle()->build());
        $articleRepository->method('findAll')->willReturn([
            anArticle()->withId('symfony-certification')->build(),
            anArticle()->withId('doctrine-query-builder-setmaxresults')->build(),
        ]);

        $articlePreviewRepository = $this->createStub(ArticlePreviewRepository::class);
        $articlePreviewRepository->method('get')->willReturn(anArticlePreview()->build());
        $articlePreviewRepository->method('findShowcased')->willReturn(anArticlePreview()->build());
        $articlePreviewRepository->method('findAll')->willReturn([anArticlePreview()->build()]);
        $articlePreviewRepository->method('getHash')->willReturn('hash');

        $articleRepository = new CachingArticleRepository($articleCache = new ArrayAdapter(), $articleRepository);
        $articlePreviewRepository = new CachingArticlePreviewRepository($articlePreviewCache = new ArrayAdapter(), $articlePreviewRepository);
        $openGraphImageGenerator = new FakeBlogOpenGraphImageGenerator();

        $cacheWarmer = new ArticleCacheWarmer(
            $articleRepository,
            $articlePreviewRepository,
            $openGraphImageGenerator,
            $this->fs,
            $this->articlesDir,
            $this->publicDir,
        );

        $this->fs->touch(sprintf('%s/1.md.twig', $this->articlesDir));
        $this->fs->touch(sprintf('%s/2.md.twig', $this->articlesDir));
        $this->fs->touch(sprintf('%s/3.md', $this->articlesDir));
        $this->fs->touch(sprintf('%s/4.txt', $this->articlesDir));

        $this->assertFileDoesNotExist(sprintf('%s/open-graph/article/symfony-certification.jpg', $this->publicDir));
        $this->assertFileDoesNotExist(sprintf('%s/open-graph/article/doctrine-query-builder-setmaxresults.jpg', $this->publicDir));

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

        $this->assertFileExists(sprintf('%s/open-graph/article/symfony-certification.jpg', $this->publicDir));
        $this->assertFileExists(sprintf('%s/open-graph/article/doctrine-query-builder-setmaxresults.jpg', $this->publicDir));
    }
}
