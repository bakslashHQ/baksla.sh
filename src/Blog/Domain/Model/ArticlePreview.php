<?php

declare(strict_types=1);

namespace App\Blog\Domain\Model;

final readonly class ArticlePreview
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public Author $author,
    ) {
    }
}
