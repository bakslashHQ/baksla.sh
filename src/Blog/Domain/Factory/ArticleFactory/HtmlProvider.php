<?php

declare(strict_types=1);

namespace App\Blog\Domain\Factory\ArticleFactory;

interface HtmlProvider
{
    public function provide(string $id): string;
}
