<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\LocaleSwitcher;

final class ArticlesTest extends KernelTestCase
{
    public function testArticlesAreTranslated(): void
    {
        self::bootKernel();

        $repository = self::getContainer()->get(ArticleRepository::class);
        $localeSwitcher = self::getContainer()->get(LocaleSwitcher::class);

        /** @var list<string> $locales */
        $locales = self::getContainer()->getParameter('kernel.enabled_locales');

        $idsByLocale = [];
        foreach ($locales as $locale) {
            /** @var list<Article> $articles */
            $articles = $localeSwitcher->runWithLocale($locale, fn (): array => $repository->findAll());
            $ids = array_map(static fn (Article $a): string => $a->id, $articles);
            sort($ids);
            $idsByLocale[$locale] = $ids;
        }

        $reference = $locales[0];
        foreach ($locales as $locale) {
            $this->assertSame(
                $idsByLocale[$reference],
                $idsByLocale[$locale],
                sprintf('Articles for locale "%s" must match locale "%s": every article must be translated in every enabled locale.', $locale, $reference),
            );
        }
    }

    public function testSlugsAreUniquePerLocale(): void
    {
        self::bootKernel();

        $repository = self::getContainer()->get(ArticleRepository::class);
        $localeSwitcher = self::getContainer()->get(LocaleSwitcher::class);

        /** @var list<string> $locales */
        $locales = self::getContainer()->getParameter('kernel.enabled_locales');

        foreach ($locales as $locale) {
            /** @var list<Article> $articles */
            $articles = $localeSwitcher->runWithLocale($locale, fn (): array => $repository->findAll());
            $slugs = array_map(static fn (Article $a): string => $a->slug, $articles);

            $this->assertSame(
                array_values(array_unique($slugs)),
                $slugs,
                sprintf('Article slugs must be unique within locale "%s"; found duplicates: %s.', $locale, implode(', ', array_diff_assoc($slugs, array_unique($slugs)))),
            );
        }
    }
}
