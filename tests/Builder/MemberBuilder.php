<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\MemberId;
use Faker\Factory;

final class MemberBuilder
{
    private MemberId|NotSet $id = NotSet::VALUE;

    public function withId(MemberId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function build(): Member
    {
        $faker = Factory::create();

        /** @var MemberId $id */
        $id = $this->id !== NotSet::VALUE ? $this->id : $faker->randomElement(MemberId::cases());

        /** @var non-empty-string $firstName */
        $firstName = $faker->firstName();

        /** @var non-empty-string $lastName */
        $lastName = $faker->lastName();

        return new Member(
            id: $id,
            firstName: $firstName,
            lastName: $lastName,
        );
    }
}
