<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Domain\Model;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Model\Author;
use PHPUnit\Framework\TestCase;

final class ArticleTest extends TestCase
{
    public function testHashsAreUnique(): void
    {
        $previews = [
            new Article('id', 'title', 'description', 'html', new Author('name', 'picture', null)),
            new Article('id2', 'title', 'description', 'html', new Author('name', 'picture', null)),
            new Article('id', 'title2', 'description', 'html', new Author('name', 'picture', null)),
            new Article('id', 'title', 'description2', 'html', new Author('name2', 'picture', null)),
            new Article('id', 'title', 'description', 'html2', new Author('name2', 'picture', null)),
            new Article('id', 'title', 'description', 'html', new Author('name2', 'picture', null)),
            new Article('id', 'title', 'description', 'html', new Author('name', 'picture2', null)),
            new Article('id', 'title', 'description', 'html', new Author('name', 'picture', 'bsky')),
        ];

        $hashs = array_column($previews, 'hash');
        $uniqueHashs = array_unique($hashs);

        $this->assertSameSize($hashs, $uniqueHashs);
    }
}
