<?php

declare(strict_types=1);

namespace App\OpenSource\Domain\Repository;

use App\OpenSource\Domain\Model\ContributionStats;
use App\OpenSource\Domain\Model\MajorContribution;

interface OpenSourceRepository
{
    /**
     * @return list<ContributionStats>
     */
    public function findAllStats(): array;

    /**
     * @return list<MajorContribution>
     */
    public function findAllMajorContributions(): array;
}
