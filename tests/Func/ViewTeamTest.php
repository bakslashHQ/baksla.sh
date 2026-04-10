<?php

declare(strict_types=1);

namespace App\Tests\Func;

final class ViewTeamTest extends FunctionalTestCase
{
    public function testRedirectsToTeamTab(): void
    {
        $this->get('/team');

        $this->assertResponseStatusCodeSame(301);
        $this->assertResponseRedirects('/#team');
    }
}
