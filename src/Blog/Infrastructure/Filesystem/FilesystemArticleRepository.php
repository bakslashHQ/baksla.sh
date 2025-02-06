<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Filesystem;

use App\Blog\Domain\Exception\MissingArticleException;
use App\Blog\Domain\Factory\ArticleFactory;
use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Finder;

final readonly class FilesystemArticleRepository implements ArticleRepository
{
    public function __construct(
        private ArticleFactory $articleFactory,
        #[Autowire(param: 'app.articles_dir')]
        private string $articlesDir,
    ) {
    }

    public function get(string $id): Article
    {
        $files = iterator_to_array((new Finder())
            ->in($this->articlesDir)
            ->name(sprintf('%s.md.twig', $id)));

        if (!$file = reset($files)) {
            throw new MissingArticleException($id);
        }

        return $this->articleFactory->create($id);
    }

    public function findAll(): array
    {
        $files = (new Finder())
            ->files()
            ->in($this->articlesDir)
            ->name('*.md.twig')
            ->ignoreVCSIgnored(true);

        $articles = [];
        foreach ($files as $file) {
            $id = str_replace('.md.twig', '', $file->getFilename());
            $articles[] = $this->articleFactory->create($id);
        }

        return $articles;
    }
}
