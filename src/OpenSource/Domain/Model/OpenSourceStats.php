<?php

declare(strict_types=1);

namespace App\OpenSource\Domain\Model;

final readonly class OpenSourceStats
{
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

    public function hasProject(string $project): bool
    {
        return isset($this->stats[$project]);
    }

    public function reviewsFor(string $project): int
    {
        return $this->stats[$project]['reviews'] ?? 0;
    }

    public function pullRequestsFor(string $project): int
    {
        return $this->stats[$project]['pullRequests'] ?? 0;
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
