<?php

declare(strict_types=1);

namespace App\Team\Domain\Repository;

use App\Team\Domain\Model\Talk;

interface TalkRepository
{
    /**
     * @return list<Talk> the most recent first
     */
    public function findAll(): array;
}
