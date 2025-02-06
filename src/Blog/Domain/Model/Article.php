<?php

declare(strict_types=1);

namespace App\Blog\Domain\Model;

final readonly class Article
{
    public string $hash;

    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $html,
        public Author $author,
    ) {
        $this->hash = md5(json_encode([
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'html' => $html,
            'author' => $author,
        ]) ?: '');
    }
}
