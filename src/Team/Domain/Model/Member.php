<?php

declare(strict_types=1);

namespace App\Team\Domain\Model;

final readonly class Member
{
    /**
     * @param non-empty-string $firstName
     * @param non-empty-string $lastName
     * @param non-empty-string $picture Filename, relative to "assets/images/team/members" directory
     * @param list<SocialNetwork> $socialNetworks
     */
    public function __construct(
        public MemberId $id,
        public string $firstName,
        public string $lastName,
        public string $picture,
        public array $socialNetworks = [],
        public ?string $bio = null,
    ) {
    }
}
