<?php

declare(strict_types=1);

namespace App\OpenSource\Domain\Repository;

use App\OpenSource\Domain\Model\Project;
use App\OpenSource\Domain\Model\ProjectId;

interface ProjectRepository
{
    /**
     * @return array<value-of<ProjectId>, Project>
     */
    public function findAll(): array;
}
