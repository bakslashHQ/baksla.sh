<?php

declare(strict_types=1);

namespace App\Blog\Domain\Model;

use App\Team\Domain\Model\Member;

final readonly class Article
{
    private const int WORDS_PER_MINUTE = 220;

    public function __construct(
        public string $id,
        public string $slug,
        public string $title,
        public string $description,
        public string $html,
        public Member $author,
        public \DateTimeImmutable $publishedAt,
    ) {
    }

    public function getReadingTime(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->html)) / self::WORDS_PER_MINUTE));
    }
}
