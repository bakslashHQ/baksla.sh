<?php

declare(strict_types=1);

namespace App\Tests\Unit\Team\Infrastructure\InMemory;

use App\Team\Domain\Exception\MissingMemberException;
use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\MemberId;
use App\Team\Infrastructure\InMemory\InMemoryMemberRepository;
use PHPUnit\Framework\TestCase;

final class InMemoryMemberRepositoryTest extends TestCase
{
    public function testFindAll(): void
    {
        $repository = new InMemoryMemberRepository([aMember()->build()]);

        $this->assertContainsOnlyInstancesOf(Member::class, $repository->findAll());
    }

    public function testFindAllReturnsMembersIndexedById(): void
    {
        $repository = new InMemoryMemberRepository([aMember()->build(), aMember()->build()]);

        foreach ($repository->findAll() as $id => $member) {
            $this->assertSame($id, $member->id->value);
        }
    }

    public function testGet(): void
    {
        $member = aMember()->withId(MemberId::MathiasArlaud)->build();
        $repository = new InMemoryMemberRepository([$member]);

        $this->assertSame($member, $repository->get($member->id));

        $this->expectException(MissingMemberException::class);

        $repository->get(MemberId::RobinChalas);
    }

    public function testGetHash(): void
    {
        $memberA = aMember()->build();
        $memberB = aMember()->build();

        $repoA = new InMemoryMemberRepository([$memberA]);
        $repoB = new InMemoryMemberRepository([$memberA, $memberB]);

        $this->assertNotSame($repoA->getHash(), $repoB->getHash());
    }
}
