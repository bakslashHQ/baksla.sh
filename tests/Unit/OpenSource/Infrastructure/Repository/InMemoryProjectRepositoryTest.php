<?php

declare(strict_types=1);

namespace App\Tests\Unit\OpenSource\Infrastructure\Repository;

use App\OpenSource\Domain\Model\ProjectId;
use App\OpenSource\Infrastructure\Repository\InMemoryProjectRepository;
use PHPUnit\Framework\TestCase;

final class InMemoryProjectRepositoryTest extends TestCase
{
    public function testFindAll(): void
    {
        $repository = new InMemoryProjectRepository([aProject()->build()]);

        $this->assertCount(1, $repository->findAll());
    }

    public function testFindAllReturnsProjectsIndexedById(): void
    {
        $repository = new InMemoryProjectRepository([
            aProject()->withId(ProjectId::Symfony)->build(),
            aProject()->withId(ProjectId::WebpackEncore)->build(),
        ]);

        foreach ($repository->findAll() as $id => $project) {
            $this->assertSame($id, $project->id->value);
        }
    }

    public function testCreateDefaultCoversEveryProjectId(): void
    {
        $projects = InMemoryProjectRepository::createDefault()->findAll();

        $this->assertEqualsCanonicalizing(
            array_column(ProjectId::cases(), 'value'),
            array_keys($projects),
            'Every ProjectId must have an entry in the default catalog, otherwise it never gets any stats.',
        );
    }

    public function testCreateDefaultListsEachRepositoryOnce(): void
    {
        $repositories = array_merge(...array_column(InMemoryProjectRepository::createDefault()->findAll(), 'repositories'));

        $duplicates = array_keys(array_filter(array_count_values($repositories), fn (int $count): bool => $count > 1));

        $this->assertSame([], $duplicates, 'A repository listed under two projects has its contributions counted twice.');
    }
}
