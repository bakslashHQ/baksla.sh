<?php

declare(strict_types=1);

namespace App\Blog\Domain\Model;

final readonly class Author
{
    public function __construct(
        public string $name,
        public string $picture,
        public ?string $bsky,
    ) {
    }
}
