<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Domain\Model;

use App\Blog\Domain\Model\ArticlePreview;
use App\Team\Domain\Model\Badge;
use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Model\SocialNetwork;
use PHPUnit\Framework\TestCase;

final class ArticlePreviewTest extends TestCase
{
    public function testHashsAreUnique(): void
    {
        $previews = [
            new ArticlePreview('id', 'title', 'description', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new ArticlePreview('id2', 'title', 'description', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new ArticlePreview('id', 'title2', 'description', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new ArticlePreview('id', 'title', 'description2', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new ArticlePreview('id', 'title', 'description', new Member(MemberId::RobinChalas, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new ArticlePreview('id', 'title', 'description', new Member(MemberId::MathiasArlaud, 'firstName2', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new ArticlePreview('id', 'title', 'description', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName2', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new ArticlePreview('id', 'title', 'description', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('bar')], [Badge::symfonyAward('foo')])),
            new ArticlePreview('id', 'title', 'description', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('bar')])),
        ];

        $hashs = array_column($previews, 'hash');
        $uniqueHashs = array_unique($hashs);

        $this->assertSameSize($hashs, $uniqueHashs);
    }
}
