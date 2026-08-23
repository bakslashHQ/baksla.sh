<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\OpenSource\Domain\Model\ProjectId;
use App\OpenSource\Domain\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RefreshOpenSourceStatsTest extends KernelTestCase
{
    private const int FAKE_ISSUE_COUNT = 3;

    private string $statsFile;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $filesystem = new Filesystem();
        $this->statsFile = (string) self::getContainer()->getParameter('app.open_source_stats_file');

        if ($filesystem->exists($this->statsFile)) {
            $filesystem->remove($this->statsFile);
        }
    }

    public function testWritesAggregatedStatsFile(): void
    {
        self::getContainer()->set('github.client', $this->fakeGithubHttpClient());

        $this->assertInstanceOf(KernelInterface::class, self::$kernel);

        $tester = new CommandTester(new Application(self::$kernel)->find('open-source:refresh-stats'));
        $tester->execute([]);
        $tester->assertCommandIsSuccessful();

        $this->assertFileExists($this->statsFile);

        /** @var array<string, array{reviews: int, pullRequests: int}> $stats */
        $stats = json_decode((string) file_get_contents($this->statsFile), true, flags: \JSON_THROW_ON_ERROR);

        $projects = self::getContainer()->get(ProjectRepository::class)->findAll();

        foreach ($projects as $id => $project) {
            $expected = \count($project->repositories) * self::FAKE_ISSUE_COUNT;

            $this->assertSame($expected, $stats[$id]['reviews']);
            $this->assertSame($expected, $stats[$id]['pullRequests']);
        }

        $this->assertCount(\count(ProjectId::cases()), $stats);
    }

    private function fakeGithubHttpClient(): HttpClientInterface
    {
        return new MockHttpClient(function (string $method, string $url, array $options): MockResponse {
            /** @var string $body */
            $body = $options['body'] ?? '';

            preg_match_all('/(\w+): search\(/', $body, $matches);

            return new MockResponse((string) json_encode([
                'data' => array_fill_keys($matches[1], [
                    'issueCount' => self::FAKE_ISSUE_COUNT,
                ]),
            ]));
        });
    }
}
