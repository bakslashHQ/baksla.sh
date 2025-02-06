<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Filesystem;

use App\Blog\Domain\Exception\MissingArticleException;
use App\Blog\Domain\Factory\ArticleFactory;
use App\Blog\Domain\Factory\ArticleFactory\HtmlGenerator;
use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Blog\Infrastructure\Filesystem\FilesystemArticleRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class FilesystemArticleRepositoryTest extends TestCase
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

    public function testGet(): void
    {
        $this->fs->touch(sprintf('%s/1.md.twig', $this->articlesDir));

        $article = $this->getRepository()->get('1');

        $this->assertInstanceOf(Article::class, $article);
        $this->assertSame('1', $article->id);
    }

    public function testGetThrowsIfNotFound(): void
    {
        $this->expectException(MissingArticleException::class);
        $this->expectExceptionMessage('"1" article does not exist.');

        $this->getRepository()->get('1');
    }

    public function testFindAll(): void
    {
        $this->fs->touch(sprintf('%s/1.md.twig', $this->articlesDir));
        $this->fs->touch(sprintf('%s/2.md.twig', $this->articlesDir));
        $this->fs->touch(sprintf('%s/3.md', $this->articlesDir));
        $this->fs->touch(sprintf('%s/4.txt', $this->articlesDir));

        $articles = $this->getRepository()->findAll();

        $this->assertCount(2, $articles);
        $this->assertContainsOnlyInstancesOf(Article::class, $articles);
        $this->assertSame(['1', '2'], array_column($articles, 'id'));
    }

    private function getRepository(): FilesystemArticleRepository
    {
        $articlePreviewRepository = $this->createStub(ArticlePreviewRepository::class);
        $articlePreviewRepository->method('get')->willReturn(anArticlePreview()->build());

        $htmlGenerator = $this->createStub(HtmlGenerator::class);
        $htmlGenerator->method('generate')->willReturn('html');

        $articleFactory = new ArticleFactory($articlePreviewRepository, $htmlGenerator);

        return new FilesystemArticleRepository($articleFactory, $this->articlesDir);
    }
}
