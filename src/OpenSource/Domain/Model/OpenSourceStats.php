<?php

declare(strict_types=1);

namespace App\OpenSource\Domain\Model;

final readonly class OpenSourceStats
{
    /**
     * @var list<string>
     */
    public const array REPOS = [
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
        'api-platform/core',
        'Sylius/Sylius',
        'Sylius/Stack',
        'Sylius/SyliusGridBundle',
        'Sylius/SyliusResourceBundle',
        'lexik/LexikJWTAuthenticationBundle',
        'thephpleague/oauth2-server-bundle',
        'thephpleague/tactician',
        'thephpleague/tactician-bundle',
        'thephpleague/tactician-logger',
        'Kocal/BiomeJsBundle',
    ];

    /**
     * @param array<string, array{reviews: int, pullRequests: int}> $stats
     */
    private function __construct(
        private array $stats,
    ) {
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public static function fromJsonFile(string $filename): self
    {
        if (!is_file($filename)) {
            return self::empty();
        }

        $contents = file_get_contents($filename);
        if ($contents === false) {
            return self::empty();
        }

        /** @var array<string, array{reviews: int, pullRequests: int}> $data */
        $data = json_decode($contents, true, flags: \JSON_THROW_ON_ERROR);

        return new self($data);
    }

    public function hasRepo(string $repo): bool
    {
        return isset($this->stats[$repo]);
    }

    public function reviewsFor(string $repo): int
    {
        return $this->stats[$repo]['reviews'] ?? 0;
    }

    public function pullRequestsFor(string $repo): int
    {
        return $this->stats[$repo]['pullRequests'] ?? 0;
    }

    public function getTotalReviews(): int
    {
        return array_sum(array_column($this->stats, 'reviews'));
    }

    public function getTotalPullRequests(): int
    {
        return array_sum(array_column($this->stats, 'pullRequests'));
    }

    public function getTotal(): int
    {
        return $this->getTotalReviews() + $this->getTotalPullRequests();
    }

    public function getHoursPerContribution(int $years): int
    {
        $total = $this->getTotal();
        if ($total === 0) {
            return 0;
        }

        return (int) round($years * 365 * 24 / $total);
    }
}
