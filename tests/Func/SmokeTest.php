<?php

declare(strict_types=1);

namespace App\Tests\Func;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestWith;

final class SmokeTest extends FunctionalTestCase
{
    #[Group('smoke')]
    #[TestWith(['/', 200])]
    #[TestWith(['/legal-notices', 200])]
    #[TestWith(['/legal-notices.html', 301, '/legal-notices'])]
    public function testUrl(string $url, int $expectedStatusCode, ?string $expectedRedirectionUrl = null): void
    {
        $this->get($url);

        $this->assertResponseStatusCodeSame($expectedStatusCode);
        if ($expectedRedirectionUrl !== null && $expectedRedirectionUrl !== '' && $expectedRedirectionUrl !== '0') {
            $this->assertResponseRedirects($expectedRedirectionUrl);
        }
    }
}
