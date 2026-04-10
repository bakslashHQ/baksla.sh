<?php

declare(strict_types=1);

namespace App\Tests\Func;

final class ViewBlogTest extends FunctionalTestCase
{
    public function testRenderProperHtml(): void
    {
        $this->get('/blog');

        $this->assertResponseStatusCodeSame(200);
    }
}
