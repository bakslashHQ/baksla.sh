<?php

declare(strict_types=1);

namespace App\Team\Domain\Repository;

use App\Team\Domain\Model\Member;

interface MemberRepository
{
    /**
     * @return list<Member>
     */
    public function findAll(): array;
}
