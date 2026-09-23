<?php

declare(strict_types=1);

namespace App\Tests\Func;

use Symfony\Component\DomCrawler\Crawler;

final class LanguageSwitcherTest extends FunctionalTestCase
{
    public function testSwitcherSwapsLocaleOnAGenericPage(): void
    {
        $crawler = $this->get('/');

        $href = $this->switcherHref($crawler, 'fr');
        $this->assertStringEndsWith('/fr', $href);

        $this->get($href);
        $this->assertResponseIsSuccessful();
    }

    public function testSwitcherPointsToTheTranslatedArticleSlug(): void
    {
        $crawler = $this->get('/blog/symfony-certification');

        $href = $this->switcherHref($crawler, 'fr');
        $this->assertStringEndsWith('/fr/blog/certification-symfony', $href);

        $this->get($href);
        $this->assertResponseIsSuccessful();
    }

    public function testSwitcherPointsBackFromTheTranslatedArticle(): void
    {
        $crawler = $this->get('/fr/blog/certification-symfony');

        $href = $this->switcherHref($crawler, 'en');
        $this->assertStringEndsWith('/blog/symfony-certification', $href);

        $this->get($href);
        $this->assertResponseIsSuccessful();
    }

    private function switcherHref(Crawler $crawler, string $locale): string
    {
        $href = $crawler->filter(sprintf('div[role=group] a[hreflang=%s]', $locale))->attr('href');
        $this->assertNotNull($href, sprintf('No language switcher link found for locale "%s".', $locale));

        return $href;
    }
}
