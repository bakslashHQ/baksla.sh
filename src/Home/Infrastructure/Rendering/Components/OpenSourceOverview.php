<?php

declare(strict_types=1);

namespace App\Home\Infrastructure\Rendering\Components;

use App\OpenSource\Domain\Model\OpenSourceStats;
use App\OpenSource\Domain\Repository\ProjectRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Home:OpenSourceOverview', template: 'components/Home/OpenSourceOverview.html.twig')]
final class OpenSourceOverview
{
    public const int SINCE_YEAR = 2010;

    private OpenSourceStats $stats;

    public function __construct(
        private readonly ProjectRepository $projectRepository,
        #[Autowire(param: 'app.open_source_stats_file')]
        string $statsFile,
    ) {
        $this->stats = OpenSourceStats::fromJsonFile($statsFile);
    }

    /**
     * @return list<array{label: string, url: string, reviews: int, prs: int, sum: int}>
     */
    public function getProjects(): array
    {
        $projects = [];
        foreach ($this->projectRepository->findAll() as $project) {
            if (!$this->stats->hasProject($project->id)) {
                continue;
            }
            $reviews = $this->stats->reviewsFor($project->id);
            $prs = $this->stats->pullRequestsFor($project->id);
            $projects[] = [
                'label' => $project->label,
                'url' => $project->getUrl(),
                'reviews' => $reviews,
                'prs' => $prs,
                'sum' => $reviews + $prs,
            ];
        }

        usort($projects, fn (array $a, array $b): int => $b['sum'] <=> $a['sum']);

        return $projects;
    }

    public function getTotalReviews(): int
    {
        return $this->stats->getTotalReviews();
    }

    public function getTotalPullRequests(): int
    {
        return $this->stats->getTotalPullRequests();
    }

    public function getTotal(): int
    {
        return $this->stats->getTotal();
    }

    public function getHoursPerContribution(): int
    {
        return $this->stats->getHoursPerContribution($this->getYears());
    }

    public function getYears(): int
    {
        return max(1, (int) date('Y') - self::SINCE_YEAR);
    }
}
