<?php

declare(strict_types=1);

namespace App\Blog\Domain\Factory\ArticleFactory;

interface HtmlGenerator
{
    public function generate(string $id): string;
}
