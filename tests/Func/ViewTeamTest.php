<?php

declare(strict_types=1);

namespace App\Tests\Func;

final class ViewTeamTest extends FunctionalTestCase
{
    public function testRenderProperHtml(): void
    {
        $this->get('/team');

        $this->assertResponseStatusCodeSame(200);
    }
}
