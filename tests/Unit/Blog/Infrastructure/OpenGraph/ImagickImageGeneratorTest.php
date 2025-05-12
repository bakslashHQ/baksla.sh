<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Infrastructure\OpenGraph;

use App\Blog\Infrastructure\OpenGraph\ImagickImageGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ImagickImageGeneratorTest extends KernelTestCase
{
    public function testGenerate(): void
    {
        $article = anArticle()->build();
        $imageGenerator = self::getContainer()->get(ImagickImageGenerator::class);

        $image = $imageGenerator->generate($article);

        $this->assertNotEmpty($image);
        $this->assertIsJpeg($image);
    }

    private function assertIsJpeg(string $content): void
    {
        $this->assertTrue(str_starts_with($content, "\xFF\xD8") && substr($content, 6, 4) === 'JFIF' && str_ends_with($content, "\xFF\xD9"), 'The image is not a valid JPEG file.');
    }
}
