<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Rendering;

use App\Blog\Infrastructure\Rendering\TwigMarkdownHtmlProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

final class TwigMarkdownHtmlProviderTest extends KernelTestCase
{
    private Filesystem $fs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();
    }

    public function testProvideProperHtml(): void
    {
        /** @var TwigMarkdownHtmlProvider $provider */
        $provider = self::getContainer()->get(TwigMarkdownHtmlProvider::class);

        $articlesDir = sprintf('%s/articles', self::getContainer()->getParameter('twig.default_path'));

        $content = <<<MD
---
metadata_foo: bar
---

[home]({{ path('app_home') }})
MD;

        try {
            $this->fs->dumpFile(sprintf('%s/1.en.md.twig', $articlesDir), $content);
            $generated = $provider->provide('1');
        } finally {
            $this->fs->remove(sprintf('%s/1.en.md.twig', $articlesDir));
        }

        $this->assertMatchesRegularExpression('#<a.*href="/".*>#', $generated);
        $this->assertStringNotContainsString('metadata_foo', $generated);
    }
}
