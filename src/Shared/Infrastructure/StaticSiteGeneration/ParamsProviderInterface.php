<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\StaticSiteGeneration;

interface ParamsProviderInterface
{
    /**
     * @return iterable<array<string, mixed>>
     */
    public function provideParams(): iterable;
}
