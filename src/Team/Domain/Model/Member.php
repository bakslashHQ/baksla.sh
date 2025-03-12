<?php

declare(strict_types=1);

namespace App\Team\Domain\Model;

final readonly class Member
{
    /**
     * @param non-empty-string    $firstName
     * @param non-empty-string    $lastName
     * @param list<SocialNetwork> $socialNetworks
     * @param list<Badge>         $badges
     */
    public function __construct(
        public MemberId $id,
        public string $firstName,
        public string $lastName,
        public array $socialNetworks = [],
        public array $badges = [],
    ) {
    }

    public function getFullname(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }
}
