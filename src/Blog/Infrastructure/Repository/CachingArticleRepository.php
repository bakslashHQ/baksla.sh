<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Repository;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use Psr\Cache\CacheItemPoolInterface;

final readonly class CachingArticleRepository implements ArticleRepository
{
    private const string ALL_KEY = '_all';

    public function __construct(
        private CacheItemPoolInterface $pool,
        private ArticleRepository $decorated,
    ) {
    }

    public function get(string $id): Article
    {
        $cache = $this->pool->getItem($id);

        if (!$cache->isHit()) {
            $cache->set($this->decorated->get($id));

            $this->pool->save($cache);
        }

        /** @var Article $article */
        $article = $cache->get();

        return $article;
    }

    public function findAll(): array
    {
        $cache = $this->pool->getItem(self::ALL_KEY);

        if (!$cache->isHit()) {
            $cache->set($this->decorated->findAll());

            $this->pool->save($cache);
        }

        /** @var list<Article> $articles */
        $articles = $cache->get();

        return $articles;
    }
}
