<?php

declare(strict_types=1);

namespace App\Tests\Func;

use PHPUnit\Framework\Attributes\TestWith;

final class ViewHomeTest extends FunctionalTestCase
{
    #[TestWith(['/'])]
    #[TestWith(['/fr'])]
    public function testHeroWordsFollowTheHeadingOrder(string $path): void
    {
        $crawler = $this->get($path);

        $this->assertSame([
            'home.hero.words.maintainers',
            'home.hero.words.lovers',
            'home.hero.words.advocates',
            'home.hero.words.developers',
        ], json_decode($crawler->filter('[data-controller="hero"]')->attr('data-hero-words-value') ?? '', true));
    }
}
