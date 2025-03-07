<?php

declare(strict_types=1);

namespace App\Blog\Domain\Model;

use App\Team\Domain\Model\Member;

final readonly class Article
{
    public string $hash;

    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $html,
        public Member $author,
    ) {
        $this->hash = hash('xxh128', json_encode($this) ?: '');
    }
}
