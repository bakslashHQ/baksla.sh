<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ArticlesTest extends KernelTestCase
{
    public function testEverySlugIsUnique(): void
    {
        self::bootKernel();

        $repository = self::getContainer()->get(ArticleRepository::class);

        $slugs = array_map(static fn (Article $article): string => $article->slug, $repository->findAll());

        $this->assertSame(
            array_values(array_unique($slugs)),
            $slugs,
            sprintf('Article slugs must be unique; found duplicates: %s.', implode(', ', array_diff_assoc($slugs, array_unique($slugs)))),
        );
    }
}
