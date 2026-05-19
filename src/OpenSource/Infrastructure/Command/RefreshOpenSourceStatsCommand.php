<?php

declare(strict_types=1);

namespace App\OpenSource\Infrastructure\Command;

use App\OpenSource\Domain\Model\OpenSourceStats;
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
        private MemberRepository $memberRepository,
        private Filesystem $filesystem,
        #[Autowire(param: 'app.open_source_stats_file')]
        private string $statsFile,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $counts = $this->githubClient->countPullRequests(
            OpenSourceStats::REPOS,
            array_values(array_map(fn (Member $member): string => $member->github, $this->memberRepository->findAll())),
        );

        $previous = OpenSourceStats::fromJsonFile($this->statsFile);

        $stats = [];
        foreach (OpenSourceStats::REPOS as $repo) {
            // GitHub's search.issueCount is an estimate for large result sets and drifts run-to-run;
            // ratchet upward so the published numbers never regress.
            $stats[$repo] = [
                'reviews' => max($previous->reviewsFor($repo), $counts[$repo]->reviewed),
                'pullRequests' => max($previous->pullRequestsFor($repo), $counts[$repo]->authored),
            ];
        }

        $this->filesystem->dumpFile($this->statsFile, json_encode($stats, \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR) . "\n");

        $io->success(\sprintf('Wrote stats to "%s"', $this->statsFile));

        return Command::SUCCESS;
    }
}
