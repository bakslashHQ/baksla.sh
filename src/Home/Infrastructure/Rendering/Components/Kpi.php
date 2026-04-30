<?php

declare(strict_types=1);

namespace App\Home\Infrastructure\Rendering\Components;

use App\Team\Domain\Model\Member;
use App\Team\Domain\Repository\MemberRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Home:Kpi', template: 'components/Home/Kpi.html.twig')]
final readonly class Kpi
{
    public function __construct(
        private MemberRepository $memberRepository,
    ) {
    }

    /**
     * @return list<array{value: int|string, suffix: string, label: string, hint: string}>
     */
    public function getKpis(): array
    {
        $memberCount = \count($this->memberRepository->findAll());
        $symfonyCoreTeamCount = \count(array_filter($this->memberRepository->findAll(), static fn (Member $m): bool => $m->isSymfonyCoreTeamMember));

        return [
            [
                'value' => 20,
                'suffix' => '+',
                'label' => 'home.kpi.projects',
                'hint' => 'home.kpi.projects_hint',
            ],
            [
                'value' => $memberCount,
                'suffix' => '',
                'label' => 'home.kpi.collaborators',
                'hint' => 'home.kpi.collaborators_hint',
            ],
            [
                'value' => $symfonyCoreTeamCount,
                'suffix' => '',
                'label' => 'home.kpi.symfony_core_team',
                'hint' => 'home.kpi.symfony_core_team_hint',
            ],
            [
                'value' => 25,
                'suffix' => '+',
                'label' => 'home.kpi.talks',
                'hint' => 'home.kpi.talks_hint',
            ],
            [
                'value' => '∞',
                'suffix' => '',
                'label' => 'home.kpi.love_in_ecosystem',
                'hint' => 'home.kpi.love_in_ecosystem_hint',
            ],
        ];
    }
}
