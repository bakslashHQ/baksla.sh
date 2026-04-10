<?php

declare(strict_types=1);

namespace App\OpenSource\Domain\Model;

use App\Team\Domain\Model\MemberId;

final readonly class MajorContribution
{
    public function __construct(
        public string $name,
        public string $description,
        public string $url,
        public Project $project,
        public MemberId $creator,
    ) {
    }
}
