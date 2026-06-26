<?php

declare(strict_types=1);

namespace App\Blog\Domain\Factory\ArticleFactory;

use App\Team\Domain\Model\MemberId;

final readonly class ArticleMetadata
{
    public function __construct(
        public string $slug,
        public string $title,
        public string $description,
        public MemberId $authorId,
        public \DateTimeImmutable $publishedAt,
    ) {
    }
}
