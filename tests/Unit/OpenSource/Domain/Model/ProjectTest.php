<?php

declare(strict_types=1);

namespace App\Tests\Unit\OpenSource\Domain\Model;

use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    public function testPullRequestsUrlSearchesEveryRepositoryForEveryAuthor(): void
    {
        $project = aProject()->withRepositories('symfony/ux', 'symfony/ux.symfony.com')->build();

        $this->assertSame(
            'https://github.com/search?type=pullrequests&q=is%3Apr%20repo%3Asymfony%2Fux%20repo%3Asymfony%2Fux.symfony.com%20author%3AKocal%20author%3Amtarld',
            $project->getPullRequestsUrl(['Kocal', 'mtarld']),
        );
    }

    public function testPullRequestsUrlAppliesTheGivenFilters(): void
    {
        $project = aProject()->withRepositories('symfony/symfony')->build();

        $this->assertSame(
            'https://github.com/search?type=pullrequests&q=is%3Apr%20is%3Amerged%20label%3ABug%20repo%3Asymfony%2Fsymfony%20author%3Achalasr',
            $project->getPullRequestsUrl(['chalasr'], ['is:merged', 'label:Bug']),
        );
    }
}
