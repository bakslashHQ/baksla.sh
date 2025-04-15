<?php

declare(strict_types=1);

namespace App\Team\Infrastructure\InMemory;

use App\Team\Domain\Exception\MissingMemberException;
use App\Team\Domain\Model\Badge;
use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Model\SocialNetwork;
use App\Team\Domain\Repository\MemberRepository;

final readonly class InMemoryMemberRepository implements MemberRepository
{
    /**
     * @var array<value-of<MemberId>, Member>
     */
    private array $members;

    /**
     * @param list<Member> $members
     */
    public function __construct(array $members = [])
    {
        $indexedMembers = [];

        foreach ($members as $member) {
            $indexedMembers[$member->id->value] = $member;
        }

        $this->members = $indexedMembers;
    }

    public static function createDefault(): self
    {
        return new self([
            new Member(MemberId::ArnaudDeAbreu, 'Arnaud', 'De Abreu', [
                SocialNetwork::github('arnaud-deabreu'),
                SocialNetwork::symfony('arnaud-deabreu'),
                SocialNetwork::linkedin('arnaud-de-abreu'),
            ], [
                Badge::phpAward('Certified'),
            ]),
            new Member(MemberId::FelixEymonot, 'Félix', 'Eymonot', [
                SocialNetwork::github('feymo'),
                SocialNetwork::symfony('hyanda'),
                SocialNetwork::linkedin('felix-eymonot'),
            ], [
                Badge::symfonyAward('Certified'),
            ]),
            new Member(MemberId::HugoAlliaume, 'Hugo', 'Alliaume', [
                SocialNetwork::github('Kocal'),
                SocialNetwork::twitter('HugoAlliaume'),
                SocialNetwork::symfony('kocal'),
                SocialNetwork::bluesky('hugo.alliau.me'),
                SocialNetwork::linkedin('hugo-alliaume'),
            ], [
                Badge::symfonyAward('UX Core Team'),
            ]),
            new Member(MemberId::JeremyRomey, 'Jérémy', 'Romey', [
                SocialNetwork::github('jeremyfreeagent'),
                SocialNetwork::twitter('jeremyfreeagent'),
                SocialNetwork::symfony('jeremyfreeagent'),
                SocialNetwork::bluesky('jeremyfreeagent.bsky.social'),
                SocialNetwork::linkedin('jeremyfreeagent'),
            ], [
                Badge::symfonyAward('Certified'),
            ]),
            new Member(MemberId::JulesPietri, 'Jules', 'Pietri', [
                SocialNetwork::github('HeahDude'),
                SocialNetwork::symfony('heah'),
                SocialNetwork::bluesky('heahdude.bsky.social'),
            ], [
                Badge::symfonyAward('Certified'),
                Badge::symfonyAward('Former Core Team'),
            ]),
            new Member(MemberId::MathiasArlaud, 'Mathias', 'Arlaud', [
                SocialNetwork::github('mtarld'),
                SocialNetwork::symfony('mtarld'),
                SocialNetwork::twitter('matarld'),
                SocialNetwork::bluesky('mtarld.bsky.social'),
                SocialNetwork::linkedin('matarld'),
            ], [
                Badge::bakslashPosition('Co-Founder / Consultant'),
                Badge::symfonyAward('Core Team'),
                Badge::symfonyAward('Certified'),
            ]),
            new Member(MemberId::RobinChalas, 'Robin', 'Chalas', [
                SocialNetwork::github('chalasr'),
                SocialNetwork::symfony('chalas_r'),
                SocialNetwork::twitter('chalas_r'),
                SocialNetwork::bluesky('chalasr.bsky.social'),
                SocialNetwork::linkedin('robinchalas'),
            ], [
                Badge::bakslashPosition('Co-Founder / Consultant'),
                Badge::symfonyAward('Core Team'),
            ]),
            new Member(MemberId::ValmontPehautPietri, 'Valmont', 'Pehaut Pietri', [
                SocialNetwork::github('Valmonzo'),
                SocialNetwork::symfony('Valmonzo'),
                SocialNetwork::twitter('valmontpp'),
                SocialNetwork::bluesky('valmonzo.bsky.social'),
                SocialNetwork::linkedin('valmontpp'),
            ]),
            new Member(MemberId::YazidHassani, 'Yazid', 'Hassani', [
                SocialNetwork::linkedin('yazid-hassani-71975416b'),
            ]),
        ]);
    }

    public function findAll(): array
    {
        return $this->members;
    }

    public function get(MemberId $id): Member
    {
        return $this->members[$id->value] ?? throw new MissingMemberException($id);
    }

    public function getHash(): string
    {
        return hash('xxh128', json_encode($this->members) ?: '');
    }
}
