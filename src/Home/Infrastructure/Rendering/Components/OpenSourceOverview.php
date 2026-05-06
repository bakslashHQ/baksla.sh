<?php

declare(strict_types=1);

namespace App\Home\Infrastructure\Rendering\Components;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Home:OpenSourceOverview', template: 'components/Home/OpenSourceOverview.html.twig')]
final readonly class OpenSourceOverview
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

    public function __construct(
        #[Autowire(param: 'app.open_source_stats_file')]
        private string $statsFile,
    ) {
    }

    /**
     * @return list<array{id: string, label: string, short: string, url: string, reviews: int, prs: int, sum: int}>
     */
    public function getProjects(): array
    {
        if (!is_file($this->statsFile)) {
            return [];
        }

        $contents = file_get_contents($this->statsFile);
        if ($contents === false) {
            return [];
        }

        /** @var array<string, array{reviews: int, pullRequests: int}> $stats */
        $stats = json_decode($contents, true, flags: \JSON_THROW_ON_ERROR);

        $projects = [];
        foreach (self::META as $id => $meta) {
            if (!isset($stats[$id])) {
                continue;
            }
            $reviews = $stats[$id]['reviews'];
            $prs = $stats[$id]['pullRequests'];
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
        return array_sum(array_column($this->getProjects(), 'reviews'));
    }

    public function getTotalPullRequests(): int
    {
        return array_sum(array_column($this->getProjects(), 'prs'));
    }

    public function getTotal(): int
    {
        return $this->getTotalReviews() + $this->getTotalPullRequests();
    }

    public function getHoursPerContribution(): int
    {
        $total = $this->getTotal();
        if ($total === 0) {
            return 0;
        }

        return (int) round($this->getYears() * 365 * 24 / $total);
    }

    public function getYears(): int
    {
        return max(1, (int) date('Y') - self::SINCE_YEAR);
    }
}
