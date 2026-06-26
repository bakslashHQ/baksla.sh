<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Component\Translation\LocaleSwitcher;

final class ViewFeedTest extends FunctionalTestCase
{
    public function testServesAsAtom(): void
    {
        $this->get('/blog/feed.xml');

        $contentType = $this->getResponse()->headers->get('Content-Type') ?? '';
        $this->assertStringContainsString('application/atom+xml', $contentType);
    }

    public function testFeedIsValidXml(): void
    {
        $this->get('/blog/feed.xml');

        $xml = simplexml_load_string($this->getResponse()->getContent() ?: '');

        $this->assertNotFalse($xml);
        $this->assertSame('feed', $xml->getName());
    }

    public function testFeedListsEveryArticle(): void
    {
        $articleRepository = $this->getService(ArticleRepository::class);
        $articles = $articleRepository->findAll();

        $this->get('/blog/feed.xml');

        $xml = simplexml_load_string($this->getResponse()->getContent() ?: '');
        $this->assertNotFalse($xml);

        $entries = $xml->entry;
        $this->assertCount(count($articles), $entries);

        $entryUrls = [];
        foreach ($entries as $entry) {
            $entryUrls[] = (string) $entry->link->attributes()['href'];
        }

        foreach ($articles as $article) {
            $expected = sprintf('https://localhost/blog/%s', $article->id);
            $this->assertContains($expected, $entryUrls);
        }
    }

    public function testEntriesCarryAuthorTitleAndSummary(): void
    {
        $articleRepository = $this->getService(ArticleRepository::class);

        $this->get('/blog/feed.xml');

        $xml = simplexml_load_string($this->getResponse()->getContent() ?: '');
        $this->assertNotFalse($xml);

        $byLink = [];
        foreach ($xml->entry as $entry) {
            $byLink[(string) $entry->link->attributes()['href']] = $entry;
        }

        foreach ($articleRepository->findAll() as $article) {
            $entry = $byLink[sprintf('https://localhost/blog/%s', $article->slug)];

            $this->assertSame($article->title, (string) $entry->title);
            $this->assertSame($article->description, (string) $entry->summary);
            $this->assertSame($article->author->getFullname(), (string) $entry->author->name);
            $this->assertSame($article->publishedAt->format('c'), (string) $entry->published);
        }
    }

    public function testFrenchFeedListsFrenchArticles(): void
    {
        $articleRepository = $this->getService(ArticleRepository::class);

        $this->get('/fr/blog/feed.xml');

        $this->assertResponseIsSuccessful();

        $xml = simplexml_load_string($this->getResponse()->getContent() ?: '');
        $this->assertNotFalse($xml);
        $this->assertSame('fr', (string) $xml->attributes('xml', true)['lang']);
        $this->assertStringContainsString('/fr/blog/feed.xml', (string) $xml->link[0]->attributes()['href']);

        $titles = [];
        foreach ($xml->entry as $entry) {
            $titles[] = (string) $entry->title;
        }

        /** @var list<Article> $frenchArticles */
        $frenchArticles = $this->getService(LocaleSwitcher::class)
            ->runWithLocale('fr', fn () => $articleRepository->findAll());
        foreach ($frenchArticles as $article) {
            $this->assertContains($article->title, $titles);
        }
    }

    public function testFeedAdvertisesEachLanguageFeed(): void
    {
        $this->get('/blog/feed.xml');

        $xml = simplexml_load_string($this->getResponse()->getContent() ?: '');
        $this->assertNotFalse($xml);

        $alternates = [];
        foreach ($xml->link as $link) {
            if ((string) $link->attributes()['rel'] === 'alternate' && (string) $link->attributes()['type'] === 'application/atom+xml') {
                $alternates[(string) $link->attributes()['hreflang']] = (string) $link->attributes()['href'];
            }
        }

        $this->assertSame('https://localhost/blog/feed.xml', $alternates['en'] ?? null);
        $this->assertSame('https://localhost/fr/blog/feed.xml', $alternates['fr'] ?? null);
    }
}
