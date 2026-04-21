<?php

declare(strict_types=1);

namespace App\OpenSource\Infrastructure\GitHub;

use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GitHubClient
{
    public function __construct(
        #[Target('github.client')]
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Counts, for each given repository, how many pull requests were authored and reviewed by the given users.
     *
     * @param list<string> $repos
     * @param list<string> $usernames
     *
     * @return array<string, PullRequestsCount> Map of repo => counts
     */
    public function countPullRequests(array $repos, array $usernames): array
    {
        if ($repos === []) {
            return [];
        }

        $queries = [];
        foreach ($repos as $index => $repo) {
            $queries['authored_' . $index] = \sprintf('repo:%s is:pr %s', $repo, implode(' ', array_map(static fn (string $u): string => \sprintf('author:%s', $u), $usernames)));
            $queries['reviewed_' . $index] = \sprintf('repo:%s is:pr %s', $repo, implode(' ', array_map(static fn (string $u): string => \sprintf('reviewed-by:%s', $u), $usernames)));
        }

        $counts = $this->fetchIssueCounts($queries);

        $result = [];
        foreach ($repos as $index => $repo) {
            $result[$repo] = new PullRequestsCount(
                authored: $counts['authored_' . $index] ?? 0,
                reviewed: $counts['reviewed_' . $index] ?? 0,
            );
        }

        return $result;
    }

    /**
     * @param array<string, string> $queries Map of alias => GitHub search query
     *
     * @return array<string, non-negative-int> Map of alias => issueCount
     */
    private function fetchIssueCounts(array $queries): array
    {
        $lines = ['query {'];
        foreach ($queries as $alias => $searchQuery) {
            $lines[] = \sprintf('  %s: search(type: ISSUE, query: %s) { issueCount }', $alias, json_encode($searchQuery, \JSON_THROW_ON_ERROR));
        }
        $lines[] = '}';

        $response = $this->httpClient->request('POST', '/graphql', [
            'json' => [
                'query' => implode("\n", $lines),
            ],
        ]);

        /** @var array{data?: array<string, array{issueCount: int}>, errors?: list<array{message: string}>} $payload */
        $payload = $response->toArray();

        if (isset($payload['errors'])) {
            throw new \RuntimeException(\sprintf('GitHub GraphQL errors: %s', implode(', ', array_column($payload['errors'], 'message'))));
        }

        $counts = [];
        foreach ($payload['data'] ?? [] as $alias => $result) {
            $counts[$alias] = max(0, $result['issueCount']);
        }

        return $counts;
    }
}
