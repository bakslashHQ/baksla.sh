<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Symfony\HttpKernel\CacheWarmer;

use App\Blog\Domain\Repository\ArticlePreviewRepository;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

final readonly class ArticleCacheWarmer implements CacheWarmerInterface
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private ArticlePreviewRepository $articlePreviewRepository,
        #[Autowire(param: 'app.articles_dir')]
        private string $articlesDir,
    ) {
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $files = (new Finder())
            ->files()
            ->in($this->articlesDir)
            ->name('*.md.twig')
            ->ignoreVCSIgnored(true);

        foreach ($files as $file) {
            $id = str_replace('.md.twig', '', $file->getFilename());

            // these methods will warm the related cache
            $this->articlePreviewRepository->get($id);
            $this->articleRepository->get($id);
        }

        // these methods will warm the related cache
        $this->articlePreviewRepository->findShowcased();
        $this->articlePreviewRepository->findAll();
        $this->articlePreviewRepository->getHash();

        return [];
    }

    public function isOptional(): bool
    {
        return false;
    }
}
