<?php

declare(strict_types=1);

namespace App\Tests\Func;

use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;

final class ViewHomeTest extends FunctionalTestCase
{
    private string $statsFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->statsFile = (string) self::getContainer()->getParameter('app.open_source_stats_file');
        new Filesystem()->dumpFile($this->statsFile, (string) json_encode([
            'symfony' => [
                'reviews' => 2,
                'pullRequests' => 3,
            ],
            'symfony-ux' => [
                'reviews' => 1,
                'pullRequests' => 1,
            ],
        ]));
    }

    protected function tearDown(): void
    {
        new Filesystem()->remove($this->statsFile);

        parent::tearDown();
    }

    #[TestWith(['/'])]
    #[TestWith(['/fr'])]
    public function testHeroWordsFollowTheHeadingOrder(string $path): void
    {
        $crawler = $this->get($path);

        $this->assertSame([
            'home.hero.words.lovers',
            'home.hero.words.maintainers',
            'home.hero.words.advocates',
            'home.hero.words.developers',
        ], json_decode($crawler->filter('[data-controller="hero"]')->attr('data-hero-words-value') ?? '', true));
    }

    #[TestWith(['/'])]
    #[TestWith(['/fr'])]
    public function testHeroShowsSymfonyCoreTeamMembersWithVerifiableLinks(string $path): void
    {
        $links = $this->hrefs($this->get($path)->filter('[data-test-maintainers] a'));

        $this->assertSame([
            'https://symfony.com/doc/current/contributing/core_team.html#active-core-members',
            'https://github.com/Kocal',
            'https://github.com/mtarld',
            'https://github.com/chalasr',
            $path === '/' ? '/team' : '/fr/equipe',
        ], $links);
    }

    public function testHeroShowsTheSymfonyCoreTeamOfEachMaintainer(): void
    {
        $roles = $this->get('/')->filter('[data-test-maintainer-role]')->each(static fn (Crawler $role): string => $role->text());

        $this->assertSame(['UX Core Team · @Kocal', 'Core Team · @mtarld', 'Core Team · @chalasr'], $roles);
    }

    public function testHeroShowsTheFaceOfEveryOtherTeammate(): void
    {
        $teammates = $this->get('/')->filter('[data-test-teammate]')->each(static fn (Crawler $face): string => (string) $face->attr('alt'));

        $this->assertSame([
            'Enzo Santamaria',
            'Félix Eymonot',
            'Florian Merle',
            'Jérémy Romey',
            'Valmont Pehaut Pietri',
            'Yazid Hassani',
        ], $teammates);
    }

    #[TestWith(['/', '/blog/fixing-symfony-ux-security-vulnerabilities'])]
    #[TestWith(['/fr', '/fr/blog/corriger-les-vulnerabilites-de-securite-symfony-ux'])]
    public function testEveryValueLinksToAPublicProof(string $path, string $securityArticleUrl): void
    {
        $crawler = $this->get($path);
        $anchors = $crawler->filter('#values [data-test-proofs] a');
        $links = $this->hrefs($anchors);

        $this->assertSame(['_blank', '_blank', '_blank', '_blank', '_blank', '_blank', ''], $anchors->each(static fn (Crawler $link): string => (string) $link->attr('target')));
        $this->assertStringStartsWith('https://github.com/search?type=pullrequests&q=is%3Apr%20repo%3Asymfony%2Fsymfony%20', $links[0]);
        $this->assertStringContainsString('author%3Amtarld', $links[0]);
        $this->assertSame([
            'https://github.com/symfony/symfony/pull/52510',
            'https://github.com/symfony/symfony/pull/51718',
            'https://github.com/symfony/symfony/pull/39802',
            'https://github.com/symfony/ux/pull/1937',
            'https://github.com/symfony/ux/pull/616',
            $securityArticleUrl,
        ], \array_slice($links, 1));
    }

    public function testEveryAuthoredProofIsSignedByItsAuthor(): void
    {
        $authors = $this->get('/')->filter('#values [data-test-proof-author]')->each(static fn (Crawler $author): string => $author->text());

        $this->assertSame(['· Mathias', '· Mathias', '· Robin', '· Hugo', '· Hugo', '· Hugo'], $authors);
    }

    public function testEveryOpenSourceLineLinksToTheMatchingGitHubSearch(): void
    {
        $links = $this->hrefs($this->get('/')->filter('#oss li a'));

        $this->assertCount(2, $links);
        foreach ($links as $link) {
            $this->assertStringStartsWith('https://github.com/search?type=pullrequests&q=is%3Apr%20', $link);
            $this->assertStringContainsString('author%3Achalasr', $link);
        }
        $this->assertStringContainsString('repo%3Asymfony%2Fsymfony%20', $links[0]);
        $this->assertStringContainsString('repo%3Asymfony%2Fux%20', $links[1]);
    }

    /**
     * @return list<string>
     */
    private function hrefs(Crawler $links): array
    {
        return $links->each(static fn (Crawler $link): string => (string) $link->attr('href'));
    }
}
