<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Filesystem;

use App\Blog\Domain\Exception\MissingArticleException;
use App\Blog\Domain\Factory\ArticlePreviewFactory;
use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Infrastructure\Filesystem\FilesystemArticlePreviewRepository;
use App\Team\Domain\Model\MemberId;
use App\Team\Infrastructure\InMemory\InMemoryMemberRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class FilesystemArticlePreviewRepositoryTest extends TestCase
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
        $this->createArticleFile('1.md.twig');

        $preview = $this->getRepository()->get('1');

        $this->assertInstanceOf(ArticlePreview::class, $preview);
        $this->assertSame('1', $preview->id);
    }

    public function testGetThrowsIfNotFound(): void
    {
        $this->expectException(MissingArticleException::class);
        $this->expectExceptionMessage('"1" article does not exist.');

        $this->getRepository()->get('1');
    }

    public function testFindShowcased(): void
    {
        $this->createArticleFile('1.md.twig');

        $showcased = $this->getRepository()->findShowcased();
        $this->assertNull($showcased);

        $showcased = $this->getRepository('1')->findShowcased();

        $this->assertInstanceOf(ArticlePreview::class, $showcased);
        $this->assertSame('1', $showcased->id);
    }

    public function testFindShowcasedThrowsIfNotFound(): void
    {
        $this->expectException(MissingArticleException::class);
        $this->expectExceptionMessage('"1" article does not exist.');

        $this->getRepository('1')->findShowcased();
    }

    public function testFindAll(): void
    {
        $this->createArticleFile('1.md.twig');
        $this->createArticleFile('2.md.twig');
        $this->createArticleFile('3.md');
        $this->createArticleFile('4.txt');

        $previews = $this->getRepository()->findAll();

        $this->assertCount(2, $previews);
        $this->assertContainsOnlyInstancesOf(ArticlePreview::class, $previews);
        $this->assertSame(['1', '2'], array_column($previews, 'id'));
    }

    public function testGetHash(): void
    {
        $hash = $this->getRepository()->getHash();

        $this->createArticleFile('1.md.twig');
        $hash2 = $this->getRepository()->getHash();

        $this->createArticleFile('2.md.twig');
        $hash3 = $this->getRepository()->getHash();

        $this->assertNotSame($hash, $hash2);
        $this->assertNotSame($hash2, $hash3);
    }

    private function getRepository(?string $showcasedArticle = null): FilesystemArticlePreviewRepository
    {
        return new FilesystemArticlePreviewRepository(new ArticlePreviewFactory(new InMemoryMemberRepository([aMember()->withId(MemberId::MathiasArlaud)->build()])), $showcasedArticle, $this->articlesDir);
    }

    private function createArticleFile(string $filename): void
    {
        $validContent = <<<MD
---
author: mathias-arlaud
title: Title
description: Description
---
MD;

        $this->fs->dumpFile(sprintf('%s/%s', $this->articlesDir, $filename), $validContent);
    }
}
