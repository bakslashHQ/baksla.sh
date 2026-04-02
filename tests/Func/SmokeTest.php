<?php

declare(strict_types=1);

namespace App\Tests\Func;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

final class SmokeTest extends FunctionalTestCase
{
    #[Group('smoke')]
    #[DataProvider('urlDataProvider')]
    public function testUrl(string $url, int $expectedStatusCode = 200, ?string $expectedRedirectionUrl = null): void
    {
        $this->get($url);

        $this->assertResponseStatusCodeSame($expectedStatusCode);

        if (!in_array($expectedRedirectionUrl, [null, '', '0'], true)) {
            $this->assertResponseRedirects($expectedRedirectionUrl);
        }
    }

    /**
     * @return iterable<array{0: string, 1?: int, 2?: string|null}>
     */
    public static function urlDataProvider(): iterable
    {
        yield ['/'];
        yield ['/sitemap.xml'];
        yield ['/robots.txt'];
        yield ['/legal-notices'];
        yield ['/legal-notices.html', 301, '/legal-notices'];
        yield ['/blog'];
        yield ['/blog/symfony-certification'];
        yield ['/team'];
    }
}
