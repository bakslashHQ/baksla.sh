<?php

declare(strict_types=1);

namespace App\Tests\Unit\Team\Infrastructure\Repository;

use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Model\Talk;
use App\Team\Infrastructure\Repository\InMemoryTalkRepository;
use PHPUnit\Framework\TestCase;

final class InMemoryTalkRepositoryTest extends TestCase
{
    public function testFindAllReturnsTheMostRecentTalksFirst(): void
    {
        $repository = new InMemoryTalkRepository([
            new Talk('Old', 'Forum PHP 2016', new \DateTimeImmutable('2016-12-01'), 'aaaaaaaaaaa', [MemberId::JeremyRomey]),
            new Talk('Recent', 'Forum PHP 2025', new \DateTimeImmutable('2025-12-01'), 'bbbbbbbbbbb', [MemberId::FelixEymonot]),
        ]);

        $this->assertSame(['Recent', 'Old'], array_map(static fn (Talk $talk): string => $talk->title, $repository->findAll()));
    }

    public function testEveryDefaultTalkHasAThumbnail(): void
    {
        foreach (InMemoryTalkRepository::createDefault()->findAll() as $talk) {
            $this->assertFileExists(\sprintf('%s/assets/images/talks/%s.webp', \dirname(__DIR__, 5), $talk->youtubeId), $talk->title);
        }
    }

    public function testUrlPointsToTheYoutubeVideo(): void
    {
        $talk = new Talk('Talk', 'Forum PHP 2023', new \DateTimeImmutable('2023-12-11'), 'oh-pbz7BC7s', [MemberId::FlorianMerle]);

        $this->assertSame('https://www.youtube.com/watch?v=oh-pbz7BC7s', $talk->getUrl());
    }
}
