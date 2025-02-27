<?php

declare(strict_types=1);

namespace App\Team\Infrastructure\InMemory;

use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Model\SocialNetwork;
use App\Team\Domain\Repository\MemberRepository;

final readonly class InMemoryMemberRepository implements MemberRepository
{
    /**
     * @param list<Member> $members
     */
    public function __construct(
        private array $members = []
    ) {
    }

    public static function createDefault(): self
    {
        return new self([
            new Member(
                MemberId::EnzoSantamaria,
                'Enzo',
                'Santamaria',
                'enzo-santamaria.jpg',
                [
                    SocialNetwork::github('Enz000'),
                    SocialNetwork::linkedin('enzo-santamaria'),
                ]
            ),
            new Member(
                MemberId::FelixEymonot,
                'Félix',
                'Eymonot',
                'felix-eymonot.jpg',
                [
                    SocialNetwork::github('feymo'),
                    SocialNetwork::symfony('hyanda'),
                    SocialNetwork::linkedin('felix-eymonot'),
                ]
            ),
            new Member(
                MemberId::HugoAlliaume,
                'Hugo',
                'Alliaume',
                'hugo-alliaume.jpg',
                [
                    SocialNetwork::github('Kocal'),
                    SocialNetwork::twitter('HugoAlliaume'),
                    SocialNetwork::symfony('kocal'),
                    SocialNetwork::bluesky('hugo.alliau.me'),
                    SocialNetwork::linkedin('hugo-alliaume'),
                ]
            ),
            new Member(
                MemberId::JeremyRomey,
                'Jérémy',
                'Romey',
                'jeremy-romey.jpg',
                [
                    SocialNetwork::github('jeremyfreeagent'),
                    SocialNetwork::twitter('jeremyfreeagent'),
                    SocialNetwork::symfony('jeremyfreeagent'),
                    SocialNetwork::bluesky('jeremyfreeagent.bsky.social'),
                    SocialNetwork::linkedin('jeremyfreeagent'),
                ]
            ),
            new Member(
                MemberId::MathiasArlaud,
                'Mathias',
                'Arlaud',
                'mathias-arlaud.jpg',
                [
                    SocialNetwork::github('mtarld'),
                    SocialNetwork::symfony('mtarld'),
                    SocialNetwork::twitter('matarld'),
                    SocialNetwork::bluesky('mtarld.bsky.social'),
                    SocialNetwork::linkedin('matarld'),
                ]
            ),
            new Member(
                MemberId::RobinChalas,
                'Robin',
                'Chalas',
                'robin-chalas.jpg',
                [
                    SocialNetwork::github('chalasr'),
                    SocialNetwork::symfony('chalas_r'),
                    SocialNetwork::twitter('chalas_r'),
                    SocialNetwork::bluesky('chalasr.bsky.social'),
                    SocialNetwork::linkedin('robinchalas'),
                ]
            ),
            new Member(
                MemberId::ValmontPehautPietri,
                'Valmont',
                'Pehaut Pietri',
                'valmont-pehaut-pietri.jpg',
                [
                    SocialNetwork::github('Valmonzo'),
                    SocialNetwork::symfony('Valmonzo'),
                    SocialNetwork::twitter('valmontpp'),
                    SocialNetwork::bluesky('valmonzo.bsky.social'),
                    SocialNetwork::linkedin('valmontpp'),
                ]
            ),
            // Until we have a picture for Yazid
            // new Member(
            //     MemberId::YazidHassani,
            //     'Yazid',
            //     'Hassani',
            //     'yazid-hassani.jpg',
            //     []
            // ),
        ]);
    }

    public function findAll(): array
    {
        return $this->members;
    }
}
