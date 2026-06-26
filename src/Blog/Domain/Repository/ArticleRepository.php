<?php

declare(strict_types=1);

namespace App\Blog\Domain\Repository;

use App\Blog\Domain\Model\Article;

interface ArticleRepository
{
    public function get(string $id): Article;

    public function getBySlug(string $slug): Article;

    public function findShowcased(): ?Article;

    /**
     * @return list<Article>
     */
    public function findAll(): array;
}
