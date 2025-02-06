<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Cache;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;
use Psr\Cache\CacheItemPoolInterface;

final readonly class CacheArticlePreviewRepository implements ArticlePreviewRepository
{
    private const string HASH_KEY = '_hash';

    private const string ALL_KEY = '_all';

    private const string SHOWCASED_KEY = '_showcased';

    public function __construct(
        private CacheItemPoolInterface $pool,
        private ArticlePreviewRepository $decorated,
    ) {
    }

    public function get(string $id): ArticlePreview
    {
        $cache = $this->pool->getItem($id);

        if (!$cache->isHit()) {
            $cache->set($this->decorated->get($id));

            $this->pool->save($cache);
        }

        /** @var ArticlePreview $article */
        $article = $cache->get();

        return $article;
    }

    public function findShowcased(): ?ArticlePreview
    {
        $cache = $this->pool->getItem(self::SHOWCASED_KEY);

        if (!$cache->isHit()) {
            $cache->set($this->decorated->findShowcased());

            $this->pool->save($cache);
        }

        /** @var ArticlePreview|null $showcased */
        $showcased = $cache->get();

        return $showcased;
    }

    public function findAll(): array
    {
        $cache = $this->pool->getItem(self::ALL_KEY);

        if (!$cache->isHit()) {
            $cache->set($this->decorated->findAll());

            $this->pool->save($cache);
        }

        /** @var list<ArticlePreview> $articles */
        $articles = $cache->get();

        return $articles;
    }

    public function getHash(): string
    {
        $cache = $this->pool->getItem(self::HASH_KEY);

        if (!$cache->isHit()) {
            $cache->set($this->decorated->getHash());

            $this->pool->save($cache);
        }

        /** @var string $hash */
        $hash = $cache->get();

        return $hash;
    }
}
