<?php

declare(strict_types=1);

namespace App\Blog\Domain\Repository;

use App\Blog\Domain\Model\ArticlePreview;

interface ArticlePreviewRepository
{
    public function get(string $id): ArticlePreview;

    public function findShowcased(): ?ArticlePreview;

    /**
     * @return list<ArticlePreview>
     */
    public function findAll(): array;

    /**
     * @return list<ArticlePreview>
     */
    public function findLatest(int $limit, bool $excludeShowcased = false): array;
}
