<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Repository\ArticlePreviewRepository;

final class ViewSitemapTest extends FunctionalTestCase
{
    public function testRenderProperXml(): void
    {
        $articlePreviewRepository = $this->getService(ArticlePreviewRepository::class);

        $this->get('/sitemap.xml');

        $response = $this->getResponse();

        $this->assertStringContainsString('text/xml', $response->headers->get('Content-Type') ?? '');

        $xml = simplexml_load_string($response->getContent() ?: '');

        /** @var array{url: list<array{loc: string}>} $xml */
        $xml = json_decode(json_encode($xml) ?: '', associative: true);

        $urls = array_map(static fn (array $u): string => $u['loc'], $xml['url']);

        $this->assertContains('https://localhost/', $urls);
        $this->assertContains('https://localhost/blog', $urls);

        $articleUrls = array_map(static fn (ArticlePreview $a): string => sprintf('https://localhost/blog/%s', $a->id), $articlePreviewRepository->findAll());
        foreach ($articleUrls as $articleUrl) {
            $this->assertContains($articleUrl, $urls);
        }
    }
}
