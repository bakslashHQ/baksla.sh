<?php

declare(strict_types=1);

namespace App\Team\Domain\Model;

/**
 * A recorded talk given by members of the team.
 */
final readonly class Talk
{
    /**
     * @param non-empty-string           $title
     * @param non-empty-string           $event
     * @param non-empty-string           $youtubeId
     * @param non-empty-list<MemberId>   $speakers
     */
    public function __construct(
        public string $title,
        public string $event,
        public int $year,
        public string $youtubeId,
        public array $speakers,
    ) {
    }

    public function getUrl(): string
    {
        return \sprintf('https://www.youtube.com/watch?v=%s', $this->youtubeId);
    }
}
