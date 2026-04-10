<?php

declare(strict_types=1);

namespace App\OpenSource\Infrastructure\GitHub;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GitHubClient
{
    private const string BASE_URL = 'https://api.github.com';

    private ?string $githubToken;

    public function __construct(
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
        string $githubToken = '',
    ) {
        $this->githubToken = $githubToken !== '' ? $githubToken : null;
    }

    /**
     * @param list<string> $usernames
     */
    public function countPullRequests(string $repo, array $usernames): int
    {
        $authorQuery = implode(' ', array_map(fn (string $u) => sprintf('author:%s', $u), $usernames));

        return $this->cachedSearchCount(
            cacheKey: sprintf('github_prs_%s', str_replace('/', '_', $repo)),
            endpoint: '/search/issues',
            query: sprintf('repo:%s is:pr %s', $repo, $authorQuery),
        );
    }

    /**
     * @param list<string> $usernames
     */
    public function countReviews(string $repo, array $usernames): int
    {
        $reviewerQuery = implode(' ', array_map(fn (string $u) => sprintf('reviewed-by:%s', $u), $usernames));

        return $this->cachedSearchCount(
            cacheKey: sprintf('github_reviews_%s', str_replace('/', '_', $repo)),
            endpoint: '/search/issues',
            query: sprintf('repo:%s is:pr %s', $repo, $reviewerQuery),
        );
    }

    private function cachedSearchCount(string $cacheKey, string $endpoint, string $query): int
    {
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($endpoint, $query): int {
            $item->expiresAfter(86400);

            $headers = [
                'Accept' => 'application/vnd.github+json',
            ];
            if ($this->githubToken !== null) {
                $headers['Authorization'] = sprintf('Bearer %s', $this->githubToken);
            }

            $response = $this->httpClient->request('GET', self::BASE_URL . $endpoint, [
                'headers' => $headers,
                'query' => [
                    'q' => $query,
                    'per_page' => 1,
                ],
            ]);

            /** @var array{total_count: int} $data */
            $data = $response->toArray();

            return $data['total_count'];
        });
    }
}
