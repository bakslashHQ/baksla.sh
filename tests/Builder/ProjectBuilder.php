<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use App\OpenSource\Domain\Model\Project;
use App\OpenSource\Domain\Model\ProjectId;
use Faker\Factory;

final class ProjectBuilder
{
    private ProjectId|NotSet $id = NotSet::VALUE;

    /**
     * @var non-empty-list<non-empty-string>|NotSet
     */
    private array|NotSet $repositories = NotSet::VALUE;

    public function withId(ProjectId $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param non-empty-string $repository
     * @param non-empty-string ...$otherRepositories
     */
    public function withRepositories(string $repository, string ...$otherRepositories): self
    {
        $this->repositories = [$repository, ...array_values($otherRepositories)];

        return $this;
    }

    public function build(): Project
    {
        $faker = Factory::create();

        /** @var ProjectId $id */
        $id = $this->id !== NotSet::VALUE ? $this->id : $faker->randomElement(ProjectId::cases());

        /** @var non-empty-string $label */
        $label = $faker->words(2, true);

        /** @var non-empty-string $repository */
        $repository = \sprintf('%s/%s', $faker->userName(), $faker->slug(1));

        return new Project(
            id: $id,
            label: $label,
            repositories: $this->repositories !== NotSet::VALUE ? $this->repositories : [$repository],
        );
    }
}
