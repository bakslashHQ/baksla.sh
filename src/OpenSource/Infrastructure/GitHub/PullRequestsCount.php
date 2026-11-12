<?php

declare(strict_types=1);

namespace App\OpenSource\Infrastructure\GitHub;

final readonly class PullRequestsCount
{
    /**
     * @param non-negative-int $authored
     * @param non-negative-int $reviewed
     * @param non-negative-int $bugFixes authored pull requests merged with the "Bug" label
     */
    public function __construct(
        public int $authored,
        public int $reviewed,
        public int $bugFixes,
    ) {
    }
}
