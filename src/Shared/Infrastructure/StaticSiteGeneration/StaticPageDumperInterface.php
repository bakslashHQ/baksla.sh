<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\StaticSiteGeneration;

interface StaticPageDumperInterface
{
    public function dump(string $uri, string $content, ?string $format = null): void;
}
