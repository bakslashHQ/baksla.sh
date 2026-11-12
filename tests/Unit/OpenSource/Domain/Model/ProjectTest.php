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
}
