<?php

declare(strict_types=1);

namespace App\Tests\Unit\OpenSource\Infrastructure\GitHub;

use App\OpenSource\Infrastructure\GitHub\GitHubClient;
use App\OpenSource\Infrastructure\GitHub\PullRequestsCount;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class GitHubClientTest extends TestCase
{
    public function testCountPullRequestsSendsOneRequestAndMapsCountsPerRepo(): void
    {
        $capturedBody = null;
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use (&$capturedBody): MockResponse {
            $this->assertSame('POST', $method);
            $this->assertStringEndsWith('/graphql', $url);
            $capturedBody = $options['body'] ?? null;

            return new MockResponse((string) json_encode([
                'data' => [
                    'authored_0' => [
                        'issueCount' => 10,
                    ],
                    'reviewed_0' => [
                        'issueCount' => 20,
                    ],
                    'bug_fixes_0' => [
                        'issueCount' => 3,
                    ],
                    'authored_1' => [
                        'issueCount' => 5,
                    ],
                    'reviewed_1' => [
                        'issueCount' => 7,
                    ],
                    'bug_fixes_1' => [
                        'issueCount' => 1,
                    ],
                ],
            ]));
        });

        $client = new GitHubClient($httpClient);

        $counts = $client->countPullRequests(['symfony/symfony', 'api-platform/core'], ['mtarld', 'chalasr']);

        $this->assertEquals([
            'symfony/symfony' => new PullRequestsCount(authored: 10, reviewed: 20, bugFixes: 3),
            'api-platform/core' => new PullRequestsCount(authored: 5, reviewed: 7, bugFixes: 1),
        ], $counts);

        $this->assertSame(1, $httpClient->getRequestsCount());
        $this->assertIsString($capturedBody);

        /** @var array{query: string} $payload */
        $payload = json_decode($capturedBody, true, flags: \JSON_THROW_ON_ERROR);

        $this->assertStringContainsString('authored_0: search(type: ISSUE, query: "repo:symfony\/symfony is:pr author:mtarld author:chalasr")', $payload['query']);
        $this->assertStringContainsString('reviewed_0: search(type: ISSUE, query: "repo:symfony\/symfony is:pr reviewed-by:mtarld reviewed-by:chalasr")', $payload['query']);
        $this->assertStringContainsString('bug_fixes_0: search(type: ISSUE, query: "repo:symfony\\/symfony is:pr is:merged label:Bug author:mtarld author:chalasr")', $payload['query']);
        $this->assertStringContainsString('authored_1: search(type: ISSUE, query: "repo:api-platform\/core is:pr author:mtarld author:chalasr")', $payload['query']);
    }

    public function testCountPullRequestsSplitsSearchesAcrossRequests(): void
    {
        $httpClient = new MockHttpClient(static function (string $method, string $url, array $options): MockResponse {
            /** @var string $body */
            $body = $options['body'] ?? '';

            preg_match_all('/(\w+): search\(/', $body, $matches);

            return new MockResponse((string) json_encode([
                'data' => array_fill_keys($matches[1], [
                    'issueCount' => 1,
                ]),
            ]));
        });

        $repos = array_map(static fn (int $i): string => \sprintf('org/repo-%d', $i), range(1, 11));

        $counts = new GitHubClient($httpClient)->countPullRequests($repos, ['mtarld']);

        $this->assertSame(2, $httpClient->getRequestsCount());
        $this->assertEquals(array_fill_keys($repos, new PullRequestsCount(authored: 1, reviewed: 1, bugFixes: 1)), $counts);
    }
}
