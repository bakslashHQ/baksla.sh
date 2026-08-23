<?php

declare(strict_types=1);

namespace App\OpenSource\Infrastructure\Command;

use App\OpenSource\Domain\Model\OpenSourceStats;
use App\OpenSource\Domain\Repository\ProjectRepository;
use App\OpenSource\Infrastructure\GitHub\GitHubClient;
use App\Team\Domain\Model\Member;
use App\Team\Domain\Repository\MemberRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'open-source:refresh-stats',
    description: 'Fetches GitHub review/PR counts and writes them to the committed stats JSON file',
)]
final readonly class RefreshOpenSourceStatsCommand
{
    public function __construct(
        private GitHubClient $githubClient,
        private ProjectRepository $projectRepository,
        private MemberRepository $memberRepository,
        private Filesystem $filesystem,
        #[Autowire(param: 'app.open_source_stats_file')]
        private string $statsFile,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $projects = $this->projectRepository->findAll();

        $counts = $this->githubClient->countPullRequests(
            array_merge(...array_column($projects, 'repositories')),
            array_values(array_map(fn (Member $member): string => $member->github, $this->memberRepository->findAll())),
        );

        $previous = OpenSourceStats::fromJsonFile($this->statsFile);

        $stats = [];
        foreach ($projects as $project) {
            $reviews = 0;
            $pullRequests = 0;
            foreach ($project->repositories as $repo) {
                $reviews += $counts[$repo]->reviewed;
                $pullRequests += $counts[$repo]->authored;
            }

            // GitHub's search.issueCount is an estimate for large result sets and drifts run-to-run;
            // ratchet upward so the published numbers never regress.
            $stats[$project->id->value] = [
                'reviews' => max($previous->reviewsFor($project->id), $reviews),
                'pullRequests' => max($previous->pullRequestsFor($project->id), $pullRequests),
            ];
        }

        $this->filesystem->dumpFile($this->statsFile, json_encode($stats, \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR) . "\n");

        $io->success(\sprintf('Wrote stats to "%s"', $this->statsFile));

        return Command::SUCCESS;
    }
}
