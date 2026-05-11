<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Domain\Model;

use App\Blog\Domain\Model\Article;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ArticleTest extends TestCase
{
    #[DataProvider('readingTimeDataProvider')]
    public function testReadingTime(int $expected, string $html): void
    {
        $article = new Article(
            id: 'id',
            title: 'title',
            description: 'description',
            html: $html,
            author: aMember()->build(),
            publishedAt: new \DateTimeImmutable('2025-01-01'),
        );

        $this->assertSame($expected, $article->readingTime);
    }

    /**
     * @return iterable<string, array{0: int, 1: string}>
     */
    public static function readingTimeDataProvider(): iterable
    {
        yield 'empty html rounds up to one minute' => [1, ''];
        yield 'single word rounds up to one minute' => [1, '<p>hello</p>'];
        yield 'short article rounds up to one minute' => [1, '<p>' . str_repeat('word ', 50) . '</p>'];
        yield 'tags are stripped before counting' => [1, '<p>' . str_repeat('<strong>w</strong> ', 100) . '</p>'];
        yield 'long article computes minutes from word count' => [3, '<p>' . str_repeat('word ', 660) . '</p>'];
    }
}
