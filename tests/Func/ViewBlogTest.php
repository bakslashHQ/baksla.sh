<?php

declare(strict_types=1);

namespace App\Tests\Func;

final class ViewBlogTest extends FunctionalTestCase
{
    public function testRedirectsToBlogTab(): void
    {
        $this->get('/blog');

        $this->assertResponseStatusCodeSame(301);
        $this->assertResponseRedirects('/#blog');
    }
}
