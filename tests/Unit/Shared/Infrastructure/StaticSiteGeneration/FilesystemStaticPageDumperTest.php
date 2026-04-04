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

    public function testMinifyHtml(): void
    {
        $dumper = new FilesystemStaticPageDumper($this->outputDir);
        $dumper->dump('/page', "<html>\n    <!-- comment -->\n    <div>   </div>\n    <p>  hello  </p>\n</html>", 'html');

        $expectedPath = \sprintf('%s/page.html', $this->outputDir);
        $this->assertSame('<html> <div> </div> <p>  hello  </p> </html>', $this->filesystem->readFile($expectedPath));
    }

    public function testMinifyHtmlPreservesPreformattedBlocks(): void
    {
        $dumper = new FilesystemStaticPageDumper($this->outputDir);
        $html = "<html>\n    <pre>\n        code\n    </pre>\n</html>";
        $dumper->dump('/page', $html, 'html');

        $expectedPath = \sprintf('%s/page.html', $this->outputDir);
        $result = $this->filesystem->readFile($expectedPath);
        $this->assertStringContainsString("<pre>\n        code\n    </pre>", $result);
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
