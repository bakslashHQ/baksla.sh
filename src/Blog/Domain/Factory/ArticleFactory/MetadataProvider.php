<?php

declare(strict_types=1);

namespace App\Blog\Domain\Factory\ArticleFactory;

interface MetadataProvider
{
    /**
     * @throws \OutOfBoundsException
     */
    public function provide(string $id): ArticleMetadata;
}
