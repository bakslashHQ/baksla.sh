<?php

declare(strict_types=1);

namespace App\OpenSource\Domain\Model;

use App\Team\Domain\Model\MemberId;
use function Symfony\Component\String\s;

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

    public function getShortUrl(): string
    {
        return s($this->url)
            ->replace('https://github.com/', '')
            ->replace('/pull/', '#')
            ->toString();
    }
}
