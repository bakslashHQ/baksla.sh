<?php

declare(strict_types=1);

namespace App\Tests\Func;

final class SmokeTest extends FunctionalTestCase
{
    /**
     * @group smoke
     * @testWith ["/", 200]
     *           ["/legal-notices", 200]
     *           ["/legal-notices.html", 301, "/legal-notices"]
     */
    public function testUrl(string $url, int $expectedStatusCode, ?string $expectedRedirectionUrl = null): void
    {
        $this->get($url);

        $this->assertResponseStatusCodeSame($expectedStatusCode);
        if ($expectedRedirectionUrl !== null && $expectedRedirectionUrl !== '' && $expectedRedirectionUrl !== '0') {
            $this->assertResponseRedirects($expectedRedirectionUrl);
        }
    }
}
