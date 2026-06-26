<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Repository;

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
        #[Autowire(param: 'app.showcased_article')]
        private ?string $showcasedArticle,
        #[Autowire(param: 'app.articles_dir')]
        private string $articlesDir,
    ) {
    }

    public function get(string $id): Article
    {
        try {
            return $this->articleFactory->create($id);
        } catch (\OutOfBoundsException) {
            throw new MissingArticleException($id);
        }
    }

    public function getBySlug(string $slug): Article
    {
        foreach ($this->findAll() as $article) {
            if ($article->slug === $slug) {
                return $article;
            }
        }

        throw new MissingArticleException($slug);
    }

    public function findShowcased(): ?Article
    {
        if ($this->showcasedArticle === null) {
            return null;
        }

        return $this->get($this->showcasedArticle);
    }

    public function findAll(): array
    {
        $files = new Finder()
            ->files()
            ->in($this->articlesDir)
            ->name('*.md.twig')
            ->ignoreVCSIgnored(true)
            ->sortByName()
        ;

        $articles = [];
        foreach ($files as $file) {
            $id = basename($file->getFilename(), '.md.twig');
            $articles[] = $this->articleFactory->create($id);
        }

        usort($articles, static fn (Article $a, Article $b): int => $b->publishedAt <=> $a->publishedAt);

        return $articles;
    }
}
