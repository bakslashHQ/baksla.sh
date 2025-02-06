<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Twig;

use App\Blog\Infrastructure\Twig\TwigMarkdownHtmlGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

final class TwigMarkdownHtmlGeneratorTest extends KernelTestCase
{
    private Filesystem $fs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();
    }

    public function testGenerateProperHtml(): void
    {
        /** @var TwigMarkdownHtmlGenerator $generator */
        $generator = static::getContainer()->get(TwigMarkdownHtmlGenerator::class);

        $articlesDir = sprintf('%s/articles', static::getContainer()->getParameter('twig.default_path'));

        $content = <<<MD
---
metadata_foo: bar
---

[home]({{ path('app_home') }})
MD;

        try {
            $this->fs->dumpFile(sprintf('%s/1.md.twig', $articlesDir), $content);
            $generated = $generator->generate('1');
        } finally {
            $this->fs->remove(sprintf('%s/1.md.twig', $articlesDir));
        }

        $this->assertMatchesRegularExpression('#<a.*href="/".*>#', $generated);
        $this->assertStringNotContainsString('metadata_foo', $generated);
    }
}
