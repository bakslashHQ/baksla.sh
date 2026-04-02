<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure\StaticSiteGeneration;

use App\Shared\Infrastructure\StaticSiteGeneration\FilesystemStaticPageDumper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class FilesystemStaticPageDumperTest extends TestCase
{
    private string $outputDir;

    private Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->outputDir = \sprintf('%s/bakslash_ssg_test/', sys_get_temp_dir());
        $this->filesystem = new Filesystem();

        if ($this->filesystem->exists($this->outputDir)) {
            $this->filesystem->remove($this->outputDir);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->filesystem->exists($this->outputDir)) {
            $this->filesystem->remove($this->outputDir);
        }
    }

    public function testDumpContent(): void
    {
        $dumper = new FilesystemStaticPageDumper($this->outputDir);
        $dumper->dump('/page-foo', 'dummy-content');

        $expectedPath = \sprintf('%s/page-foo', $this->outputDir);

        $this->assertTrue($this->filesystem->exists($expectedPath));
        $this->assertSame('dummy-content', $this->filesystem->readFile($expectedPath));
    }

    public function testDumpRootAsIndexHtml(): void
    {
        $dumper = new FilesystemStaticPageDumper($this->outputDir);
        $dumper->dump('/', '<html></html>');

        $expectedPath = \sprintf('%s/index.html', $this->outputDir);

        $this->assertTrue($this->filesystem->exists($expectedPath));
        $this->assertSame('<html></html>', $this->filesystem->readFile($expectedPath));
    }

    public function testAppendFormat(): void
    {
        $dumper = new FilesystemStaticPageDumper($this->outputDir);
        $dumper->dump('/page-foo', 'dummy-content', 'html');

        $expectedPath = \sprintf('%s/page-foo.html', $this->outputDir);

        $this->assertTrue($this->filesystem->exists($expectedPath));
        $this->assertSame('dummy-content', $this->filesystem->readFile($expectedPath));
    }

    public function testDoNotAppendFormatIfAlreadyPresent(): void
    {
        $dumper = new FilesystemStaticPageDumper($this->outputDir);
        $dumper->dump('/sitemap.xml', '<xml/>', 'xml');

        $expectedPath = \sprintf('%s/sitemap.xml', $this->outputDir);

        $this->assertTrue($this->filesystem->exists($expectedPath));
        $this->assertSame('<xml/>', $this->filesystem->readFile($expectedPath));
    }
}
