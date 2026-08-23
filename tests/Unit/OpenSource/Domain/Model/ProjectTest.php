<?php

declare(strict_types=1);

namespace App\Tests\Unit\OpenSource\Domain\Model;

use PHPUnit\Framework\TestCase;

final class ProjectTest extends TestCase
{
    public function testUrlPointsToTheCanonicalRepository(): void
    {
        $project = aProject()->withRepositories('symfony/webpack-encore', 'symfony/webpack-encore-bundle')->build();

        $this->assertSame('https://github.com/symfony/webpack-encore', $project->getUrl());
    }
}
