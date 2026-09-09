<?php

declare(strict_types=1);

namespace App\Tests\Func;

final class PreviewOpenGraphImageTest extends FunctionalTestCase
{
    public function testRendersEnglishOpenGraphImage(): void
    {
        $this->get('/_og/blog/symfony-certification');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('html[lang="en"]');
        $this->assertSelectorTextContains('h1', 'Symfony Certification');
    }

    public function testRendersFrenchOpenGraphImage(): void
    {
        $this->get('/fr/_og/blog/symfony-certification');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('html[lang="fr"]');
        $this->assertSelectorTextContains('h1', 'La certification Symfony');
    }
}
