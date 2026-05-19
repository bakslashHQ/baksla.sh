<?php

declare(strict_types=1);

namespace App\Home\Infrastructure\Rendering\Components;

use App\OpenSource\Domain\Model\OpenSourceStats;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Home:OpenSourceOverview', template: 'components/Home/OpenSourceOverview.html.twig')]
final readonly class OpenSourceOverview
{
    public const int SINCE_YEAR = 2010;

    private OpenSourceStats $stats;

    public function __construct(
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
        foreach (OpenSourceStats::REPOS as $repo) {
            if (!$this->stats->hasRepo($repo)) {
                continue;
            }
            $reviews = $this->stats->reviewsFor($repo);
            $prs = $this->stats->pullRequestsFor($repo);
            $projects[] = [
                'label' => $repo,
                'url' => \sprintf('https://github.com/%s/graphs/contributors?all=1', $repo),
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
