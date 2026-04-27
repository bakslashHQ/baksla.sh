<?php

declare(strict_types=1);

namespace App\OpenSource\Infrastructure\Command;

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
    /**
     * @var array<string, list<string>>
     */
    public const array REPOS = [
        'symfony' => [
            'symfony/symfony',
            'symfony/symfony-docs',
            'symfony/demo',
            'symfony/polyfill',
            'symfony/recipes',
            'symfony/recipes-contrib',
            'symfony/maker-bundle',
            'symfony/monolog-bundle',
            'symfony/mercure',
            'symfony/mercure-bundle',
            'symfony/panther',
            'symfony/ux',
            'symfony/ux.symfony.com',
            'symfony/ai',
        ],
        'api-platform' => ['api-platform/core'],
        'sylius' => [
            'Sylius/Sylius',
            'Sylius/Stack',
            'Sylius/SyliusGridBundle',
            'Sylius/SyliusResourceBundle',
        ],
        'lexik-jwt' => ['lexik/LexikJWTAuthenticationBundle'],
        'oauth2-server-bundle' => ['thephpleague/oauth2-server-bundle'],
        'tactician' => [
            'thephpleague/tactician',
            'thephpleague/tactician-bundle',
            'thephpleague/tactian-logger',
        ],
        'biome-js-bundle' => ['Kocal/BiomeJsBundle'],
        'oxc-bundle' => ['Kocal/OxcBundle'],
    ];

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
            array_merge(...array_values(self::REPOS)),
            array_values(array_map(fn (Member $member): string => $member->github, $this->memberRepository->findAll())),
        );

        $stats = [];
        foreach (self::REPOS as $project => $repos) {
            $reviews = 0;
            $pullRequests = 0;
            foreach ($repos as $repo) {
                $reviews += $counts[$repo]->reviewed;
                $pullRequests += $counts[$repo]->authored;
            }

            $stats[$project] = [
                'reviews' => $reviews,
                'pullRequests' => $pullRequests,
            ];
        }

        $this->filesystem->dumpFile($this->statsFile, json_encode($stats, \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR) . "\n");

        $io->success(\sprintf('Wrote stats to "%s"', $this->statsFile));

        return Command::SUCCESS;
    }
}
