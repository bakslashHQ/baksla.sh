<?php

declare(strict_types=1);

namespace App\Team\Infrastructure\InMemory;

use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\MemberId;
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
            new Member(MemberId::ArnaudDeAbreu, 'Arnaud', 'de Abreu', 'arnaud-de-abreu.jpg', []),
            new Member(MemberId::EnzoSantamaria, 'Enzo', 'Santamaria', 'enzo-santamaria.jpg', []),
            new Member(MemberId::FelixEymonot, 'Félix', 'Eymonot', 'felix-eymonot.jpg', []),
            new Member(MemberId::HugoAlliaume, 'Hugo', 'Alliaume', 'hugo-alliaume.jpg', []),
            new Member(MemberId::JulesPietri, 'Jules', 'Pietri', 'jules-pietri.jpg', []),
            new Member(MemberId::JeremyRomey, 'Jérémy', 'Romey', 'jeremy-romey.jpg', []),
            new Member(MemberId::MathiasArlaud, 'Mathias', 'Arlaud', 'mathias-arlaud.jpg', []),
            new Member(MemberId::RobinChalas, 'Robin', 'Chalas', 'robin-chalas.jpg', []),
            new Member(MemberId::ValmontPehautPietri, 'Valmont', 'Pehaut Pietri', 'valmont-pehaut-pietri.jpg', []),
        ]);
    }

    public function findAll(): array
    {
        return $this->members;
    }
}
