<?php

declare(strict_types=1);

namespace App\Website\Infrastructure\Rendering\Components;

use App\Team\Domain\Model\Member;
use App\Team\Domain\Repository\MemberRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final readonly class Team
{
    public function __construct(
        private MemberRepository $memberRepository,
    ) {
    }

    /**
     * @return array<string, Member>
     */
    public function getMembers(): array
    {
        return $this->memberRepository->findAll();
    }
}
