<?php

declare(strict_types=1);

namespace App\Home\Infrastructure\Rendering\Components;

use App\OpenSource\Domain\Model\OpenSourceStats;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Home:OpenSourceOverview', template: 'components/Home/OpenSourceOverview.html.twig')]
final class OpenSourceOverview
{
    public const int SINCE_YEAR = 2010;

    /**
     * @var array<string, array{label: string, short: string, url: string}>
     */
    private const array META = [
        'symfony' => [
            'label' => 'Symfony',
            'short' => 'symfony/symfony',
            'url' => 'https://github.com/symfony/symfony',
        ],
        'api-platform' => [
            'label' => 'API Platform',
            'short' => 'api-platform/core',
            'url' => 'https://github.com/api-platform/core',
        ],
        'sylius' => [
            'label' => 'Sylius',
            'short' => 'Sylius/Sylius',
            'url' => 'https://github.com/Sylius/Sylius',
        ],
        'lexik-jwt' => [
            'label' => 'LexikJWTAuthBundle',
            'short' => 'lexik/jwt-auth',
            'url' => 'https://github.com/lexik/LexikJWTAuthenticationBundle',
        ],
        'oauth2-server-bundle' => [
            'label' => 'OAuth2 Server Bundle',
            'short' => 'league/oauth2-server-bundle',
            'url' => 'https://github.com/thephpleague/oauth2-server-bundle',
        ],
        'tactician' => [
            'label' => 'Tactician',
            'short' => 'thephpleague/tactician',
            'url' => 'https://github.com/thephpleague/tactician',
        ],
        'biome-js-bundle' => [
            'label' => 'BiomeJsBundle',
            'short' => 'Kocal/BiomeJsBundle',
            'url' => 'https://github.com/Kocal/BiomeJsBundle',
        ],
    ];

    private OpenSourceStats $stats;

    public function __construct(
        #[Autowire(param: 'app.open_source_stats_file')]
        string $statsFile,
    ) {
        $this->stats = OpenSourceStats::fromJsonFile($statsFile);
    }

    /**
     * @return list<array{id: string, label: string, short: string, url: string, reviews: int, prs: int, sum: int}>
     */
    public function getProjects(): array
    {
        $projects = [];
        foreach (self::META as $id => $meta) {
            if (!$this->stats->hasProject($id)) {
                continue;
            }
            $reviews = $this->stats->reviewsFor($id);
            $prs = $this->stats->pullRequestsFor($id);
            $projects[] = [
                'id' => $id,
                'label' => $meta['label'],
                'short' => $meta['short'],
                'url' => $meta['url'],
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
