<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\StaticSiteGeneration;

interface StaticPageUrisProviderInterface
{
    /**
     * @return iterable<string>
     */
    public function provide(): iterable;
}
