<?php

declare(strict_types=1);

namespace App\Blog\Domain\Model;

use App\Team\Domain\Model\Member;

final readonly class ArticlePreview
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public Member $author,
    ) {
    }
}
