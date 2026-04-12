<?php

declare(strict_types=1);

namespace App\OpenSource\Domain\Model;

final readonly class ContributionStats
{
    public function __construct(
        public Project $project,
        public int $reviews,
        public int $pullRequests,
    ) {
    }
}
