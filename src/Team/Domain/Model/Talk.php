<?php

declare(strict_types=1);

namespace App\Team\Domain\Model;

/**
 * A recorded talk given by members of the team.
 */
final readonly class Talk
{
    /**
     * @param non-empty-string         $title
     * @param non-empty-string         $event       where and when it was given, e.g. "Forum PHP 2025"
     * @param \DateTimeImmutable       $publishedAt when the recording went online, to list the most recent talks first
     * @param non-empty-string         $youtubeId
     * @param non-empty-list<MemberId> $speakers
     */
    public function __construct(
        public string $title,
        public string $event,
        public \DateTimeImmutable $publishedAt,
        public string $youtubeId,
        public array $speakers,
    ) {
    }

    public function getUrl(): string
    {
        return \sprintf('https://www.youtube.com/watch?v=%s', $this->youtubeId);
    }
}
