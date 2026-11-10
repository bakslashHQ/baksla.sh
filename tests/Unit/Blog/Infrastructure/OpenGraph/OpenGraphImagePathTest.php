<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\OpenGraph;

use App\Blog\Infrastructure\OpenGraph\OpenGraphImagePath;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class OpenGraphImagePathTest extends TestCase
{
    #[TestWith(['en', '/open-graph/article/an-article.jpg'])]
    #[TestWith(['fr', '/open-graph/article/an-article.fr.jpg'])]
    public function testOf(string $locale, string $expected): void
    {
        $this->assertSame($expected, new OpenGraphImagePath('en')->of('an-article', $locale));
    }
}
