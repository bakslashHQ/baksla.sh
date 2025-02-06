<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Filesystem;

use App\Blog\Domain\Exception\MissingArticleException;
use App\Blog\Domain\Factory\ArticlePreviewFactory;
use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;

final readonly class FilesystemArticlePreviewRepository implements ArticlePreviewRepository
{
    public function __construct(
        private ArticlePreviewFactory $articlePreviewFactory,
        #[Autowire(env: 'SHOWCASED_ARTICLE')]
        private ?string $showcasedArticle,
        #[Autowire(param: 'app.articles_dir')]
        private string $articlesDir,
    ) {
    }

    public function get(string $id): ArticlePreview
    {
        $files = iterator_to_array((new Finder())
            ->in($this->articlesDir)
            ->name(sprintf('%s.md.twig', $id)));

        if (!$file = reset($files)) {
            throw new MissingArticleException($id);
        }

        return $this->articlePreviewFactory->create($id, $file->getContents());
    }

    public function findShowcased(): ?ArticlePreview
    {
        if ($this->showcasedArticle === null || $this->showcasedArticle === '' || $this->showcasedArticle === '0') {
            return null;
        }

        return $this->get($this->showcasedArticle);
    }

    public function findAll(): array
    {
        $files = (new Finder())
            ->files()
            ->in($this->articlesDir)
            ->name('*.md.twig')
            ->ignoreVCSIgnored(true);

        $previews = [];
        foreach ($files as $file) {
            $id = str_replace('.md.twig', '', $file->getFilename());
            $previews[] = $this->articlePreviewFactory->create($id, $file->getContents());
        }

        return $previews;
    }

    public function getHash(): string
    {
        $this->findShowcased();
        $this->findAll();

        return md5(json_encode([array_column($this->findAll(), 'hash'), $this->showcasedArticle]) ?: '');
    }
}
