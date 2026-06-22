<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Repository\ArticleRepository;

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

        $byId = [];
        foreach ($xml->entry as $entry) {
            $byId[(string) $entry->id] = $entry;
        }

        foreach ($articleRepository->findAll() as $article) {
            $id = sprintf('https://localhost/blog/%s', $article->id);
            $entry = $byId[$id];

            $this->assertSame($article->title, (string) $entry->title);
            $this->assertSame($article->description, (string) $entry->summary);
            $this->assertSame($article->author->getFullname(), (string) $entry->author->name);
            $this->assertSame($article->publishedAt->format('c'), (string) $entry->published);
        }
    }
}
