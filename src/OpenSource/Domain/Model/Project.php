<?php

declare(strict_types=1);

namespace App\OpenSource\Domain\Model;

final readonly class Project
{
    /**
     * @param non-empty-string                 $label
     * @param non-empty-list<non-empty-string> $repositories GitHub "owner/name" slugs; the first one is the project's canonical repository
     */
    public function __construct(
        public ProjectId $id,
        public string $label,
        public array $repositories,
    ) {
    }

    public function getUrl(): string
    {
        return \sprintf('https://github.com/%s', $this->repositories[0]);
    }
}
