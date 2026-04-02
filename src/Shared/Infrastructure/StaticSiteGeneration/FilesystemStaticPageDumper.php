<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\StaticSiteGeneration;

use Symfony\Component\Filesystem\Filesystem;

final readonly class FilesystemStaticPageDumper implements StaticPageDumperInterface
{
    private Filesystem $filesystem;

    public function __construct(
        private string $outputDir,
    ) {
        $this->filesystem = new Filesystem();
    }

    public function dump(string $uri, string $content, ?string $format = null): void
    {
        if ($uri === '/') {
            $fileName = 'index.html';
        } elseif ($format && !str_ends_with($uri, '.' . $format)) {
            $fileName = \sprintf('%s.%s', $uri, $format);
        } else {
            $fileName = $uri;
        }

        $this->filesystem->dumpFile(\sprintf('%s/%s', $this->outputDir, $fileName), $content);
    }
}
