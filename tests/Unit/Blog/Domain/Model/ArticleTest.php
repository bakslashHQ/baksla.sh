<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Domain\Model;

use App\Blog\Domain\Model\Article;
use App\Team\Domain\Model\Badge;
use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Model\SocialNetwork;
use PHPUnit\Framework\TestCase;

final class ArticleTest extends TestCase
{
    public function testHashsAreUnique(): void
    {
        $previews = [
            new Article('id', 'title', 'description', 'html', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new Article('id2', 'title', 'description', 'html', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new Article('id', 'title2', 'description', 'html', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new Article('id', 'title', 'description2', 'html', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new Article('id', 'title', 'description', 'html2', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new Article('id', 'title', 'description', 'html', new Member(MemberId::RobinChalas, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new Article('id', 'title', 'description', 'html', new Member(MemberId::MathiasArlaud, 'firstName2', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new Article('id', 'title', 'description', 'html', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName2', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('foo')])),
            new Article('id', 'title', 'description', 'html', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('bar')], [Badge::symfonyAward('foo')])),
            new Article('id', 'title', 'description', 'html', new Member(MemberId::MathiasArlaud, 'firstName', 'lastName', [SocialNetwork::symfony('foo')], [Badge::symfonyAward('bar')])),
        ];

        $hashs = array_column($previews, 'hash');
        $uniqueHashs = array_unique($hashs);

        $this->assertSameSize($hashs, $uniqueHashs);
    }
}
