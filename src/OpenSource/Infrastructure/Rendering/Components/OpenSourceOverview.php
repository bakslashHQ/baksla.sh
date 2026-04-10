<?php

declare(strict_types=1);

namespace App\OpenSource\Infrastructure\Rendering\Components;

use App\OpenSource\Domain\Model\ContributionStats;
use App\OpenSource\Domain\Model\MajorContribution;
use App\OpenSource\Domain\Model\Project;
use App\OpenSource\Domain\Repository\OpenSourceRepository;
use App\Team\Domain\Repository\MemberRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final readonly class OpenSourceOverview
{
    public function __construct(
        private OpenSourceRepository $openSourceRepository,
        private MemberRepository $memberRepository,
    ) {
    }

    /**
     * @return list<ContributionStats>
     */
    public function getStats(): array
    {
        return $this->openSourceRepository->findAllStats();
    }

    /**
     * @return list<MajorContribution>
     */
    public function getMajorContributions(): array
    {
        return $this->openSourceRepository->findAllMajorContributions();
    }

    /**
     * @return array<string, list<MajorContribution>>
     */
    public function getContributionsByProject(): array
    {
        $grouped = [];

        foreach ($this->getMajorContributions() as $contribution) {
            $grouped[$contribution->project->value][] = $contribution;
        }

        return $grouped;
    }

    /**
     * @return list<ContributionStats>
     */
    public function getMainProjectStats(): array
    {
        return array_values(array_filter(
            $this->getStats(),
            fn (ContributionStats $s) => !$s->project->isBundle(),
        ));
    }

    /**
     * @return array{reviews: int, pullRequests: int}
     */
    public function getBundleStats(): array
    {
        $reviews = 0;
        $pullRequests = 0;

        foreach ($this->getStats() as $stats) {
            if ($stats->project->isBundle()) {
                $reviews += $stats->reviews;
                $pullRequests += $stats->pullRequests;
            }
        }

        return [
            'reviews' => $reviews,
            'pullRequests' => $pullRequests,
        ];
    }

    /**
     * @return list<Project>
     */
    public function getBundles(): array
    {
        return array_values(array_filter(Project::cases(), fn (Project $p) => $p->isBundle()));
    }

    /**
     * @return list<array{memberId: string, name: string}>
     */
    public function getReviewers(): array
    {
        $reviewers = [];

        foreach ($this->memberRepository->findAll() as $member) {
            foreach ($member->badges as $badge) {
                if ($badge->uxIconLabel === 'Symfony' && \in_array($badge->text, ['Core Team', 'UX Core Team'], true)) {
                    $reviewers[] = [
                        'memberId' => $member->id->value,
                        'name' => $member->getFullname(),
                    ];

                    break;
                }
            }
        }

        return $reviewers;
    }

    /**
     * @return list<array{label: string, color: string}>
     */
    public function getLabels(): array
    {
        return [
            [
                'label' => 'Symfony',
                'color' => 'indigo',
            ],
            [
                'label' => 'API Platform',
                'color' => 'purple',
            ],
            [
                'label' => 'Sylius',
                'color' => 'violet',
            ],
            [
                'label' => 'Feature',
                'color' => 'green',
            ],
            [
                'label' => 'Status: Reviewed',
                'color' => 'emerald',
            ],
        ];
    }
}
