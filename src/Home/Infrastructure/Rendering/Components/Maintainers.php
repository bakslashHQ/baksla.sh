<?php

declare(strict_types=1);

namespace App\Home\Infrastructure\Rendering\Components;

use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\SymfonyCoreTeam;
use App\Team\Domain\Repository\MemberRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Home:Maintainers', template: 'components/Home/Maintainers.html.twig')]
final class Maintainers
{
    public function __construct(
        private readonly MemberRepository $memberRepository,
    ) {
    }

    /**
     * @return list<Member>
     */
    public function getMaintainers(): array
    {
        return array_values(array_filter($this->memberRepository->findAll(), static fn (Member $member): bool => $member->symfonyCoreTeam instanceof SymfonyCoreTeam));
    }

    /**
     * @return list<Member>
     */
    public function getTeammates(): array
    {
        return array_values(array_filter($this->memberRepository->findAll(), static fn (Member $member): bool => !$member->symfonyCoreTeam instanceof SymfonyCoreTeam));
    }
}
