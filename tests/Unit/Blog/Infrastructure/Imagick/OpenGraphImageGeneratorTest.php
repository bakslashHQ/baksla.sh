<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\Imagick;

use App\Blog\Infrastructure\Imagick\OpenGraphImageGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OpenGraphImageGeneratorTest extends KernelTestCase
{
    public function testGenerate(): void
    {
        $article = anArticle()->build();
        $imageGenerator = self::getContainer()->get(OpenGraphImageGenerator::class);

        $image = $imageGenerator->generate($article);

        $this->assertNotEmpty($image);
        $this->assertIsJpeg($image);
    }

    private function assertIsJpeg(string $content): void
    {
        $this->assertTrue(str_starts_with($content, "\xFF\xD8") && substr($content, 6, 4) === 'JFIF' && str_ends_with($content, "\xFF\xD9"), 'The image is not a valid JPEG file.');
    }
}
