<?php

declare(strict_types=1);

namespace App\OpenSource\Infrastructure\Repository;

use App\OpenSource\Domain\Model\ContributionStats;
use App\OpenSource\Domain\Model\MajorContribution;
use App\OpenSource\Domain\Model\Project;
use App\OpenSource\Domain\Repository\OpenSourceRepository;
use App\OpenSource\Infrastructure\GitHub\GitHubClient;
use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Repository\MemberRepository;

final readonly class GitHubOpenSourceRepository implements OpenSourceRepository
{
    /**
     * @var array<string, list<string>>
     */
    private const array REPOS = [
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
        'sylius' => ['Sylius/Sylius'],
        'lexik-jwt' => ['lexik/LexikJWTAuthenticationBundle'],
        'oauth2-server-bundle' => ['thephpleague/oauth2-server-bundle'],
        'biome-js-bundle' => ['Kocal/BiomeJsBundle'],
        'oxc-bundle' => ['Kocal/OxcBundle'],
    ];

    /**
     * @var list<string>
     */
    private array $githubUsernames;

    public function __construct(
        private GitHubClient $githubClient,
        MemberRepository $memberRepository,
    ) {
        $usernames = [];

        foreach ($memberRepository->findAll() as $member) {
            foreach ($member->socialNetworks as $socialNetwork) {
                if ($socialNetwork->uxIconLabel === 'Github') {
                    $usernames[] = basename($socialNetwork->url);
                }
            }
        }

        $this->githubUsernames = $usernames;
    }

    public function findAllStats(): array
    {
        $stats = [];

        foreach (Project::cases() as $project) {
            $repos = self::REPOS[$project->value];
            $reviews = 0;
            $pullRequests = 0;

            foreach ($repos as $repo) {
                $reviews += $this->githubClient->countReviews($repo, $this->githubUsernames);
                $pullRequests += $this->githubClient->countPullRequests($repo, $this->githubUsernames);
            }

            $stats[] = new ContributionStats($project, $reviews, $pullRequests);
        }

        return $stats;
    }

    public function findAllMajorContributions(): array
    {
        return [
            new MajorContribution(
                name: 'JsonStreamer',
                description: 'A blazing fast, low-memory JSON serialization/deserialization component.',
                url: 'https://github.com/symfony/json-streamer',
                project: Project::Symfony,
                creator: MemberId::MathiasArlaud,
            ),
            new MajorContribution(
                name: 'TypeInfo',
                description: 'A component to extract and manipulate PHP type information from various sources.',
                url: 'https://github.com/symfony/type-info',
                project: Project::Symfony,
                creator: MemberId::MathiasArlaud,
            ),
            new MajorContribution(
                name: 'UX Map',
                description: 'An interactive map integration for Symfony UX with Google Maps and Leaflet support.',
                url: 'https://github.com/symfony/ux-map',
                project: Project::Symfony,
                creator: MemberId::HugoAlliaume,
            ),
            new MajorContribution(
                name: 'UX Translator',
                description: 'An integration to use Symfony translations directly in JavaScript.',
                url: 'https://github.com/symfony/ux-translator',
                project: Project::Symfony,
                creator: MemberId::HugoAlliaume,
            ),
            new MajorContribution(
                name: 'Console lazy-loading',
                description: 'Lazy-loading support for console commands, improving application boot performance.',
                url: 'https://github.com/symfony/symfony/pull/22734',
                project: Project::Symfony,
                creator: MemberId::RobinChalas,
            ),
            new MajorContribution(
                name: 'Console ArgumentResolver',
                description: 'A new way to define and resolve console command arguments and options.',
                url: 'https://github.com/symfony/symfony/pull/62917',
                project: Project::Symfony,
                creator: MemberId::RobinChalas,
            ),
            new MajorContribution(
                name: 'Command Profiling',
                description: 'Enables profiling console commands in the Symfony WebProfiler.',
                url: 'https://github.com/symfony/symfony/pull/47416',
                project: Project::Symfony,
                creator: MemberId::JulesPietri,
            ),
            new MajorContribution(
                name: 'Webpack Encore',
                description: 'A simpler way to integrate Webpack into Symfony applications.',
                url: 'https://github.com/symfony/webpack-encore',
                project: Project::Symfony,
                creator: MemberId::HugoAlliaume,
            ),
            new MajorContribution(
                name: 'UX Toolkit',
                description: 'A set of ready-to-use UI components for Symfony UX.',
                url: 'https://github.com/symfony/ux-toolkit',
                project: Project::Symfony,
                creator: MemberId::HugoAlliaume,
            ),
        ];
    }
}
