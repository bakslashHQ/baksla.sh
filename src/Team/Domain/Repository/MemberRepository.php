<?php

declare(strict_types=1);

namespace App\Team\Domain\Repository;

use App\Team\Domain\Exception\MissingMemberException;
use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\MemberId;

interface MemberRepository
{
    /**
     * @return array<value-of<MemberId>, Member>
     */
    public function findAll(): array;

    /**
     * @throws MissingMemberException
     */
    public function get(MemberId $id): Member;

    public function getHash(): string;
}
