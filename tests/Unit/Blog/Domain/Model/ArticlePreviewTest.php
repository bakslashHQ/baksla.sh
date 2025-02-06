<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Domain\Model;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Model\Author;
use PHPUnit\Framework\TestCase;

final class ArticlePreviewTest extends TestCase
{
    public function testHashsAreUnique(): void
    {
        $previews = [
            new ArticlePreview('id', 'title', 'description', new Author('name', 'picture', null)),
            new ArticlePreview('id2', 'title', 'description', new Author('name', 'picture', null)),
            new ArticlePreview('id', 'title2', 'description', new Author('name', 'picture', null)),
            new ArticlePreview('id', 'title', 'description2', new Author('name', 'picture', null)),
            new ArticlePreview('id', 'title', 'description', new Author('name2', 'picture', null)),
            new ArticlePreview('id', 'title', 'description', new Author('name', 'picture2', null)),
            new ArticlePreview('id', 'title', 'description', new Author('name', 'picture', 'bsky')),
        ];

        $hashs = array_column($previews, 'hash');
        $uniqueHashs = array_unique($hashs);

        $this->assertSameSize($hashs, $uniqueHashs);
    }
}
