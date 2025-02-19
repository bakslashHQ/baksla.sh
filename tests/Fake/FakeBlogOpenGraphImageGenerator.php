<?php

declare(strict_types=1);

namespace App\Tests\Fake;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\OpenGraph\ImageGenerator;

final readonly class FakeBlogOpenGraphImageGenerator implements ImageGenerator
{
    public function generate(Article $article): string
    {
        $image = new \Imagick();
        $image->newImage(self::WIDTH, self::HEIGHT, new \ImagickPixel('#aff'));
        $image->setImageFormat('jpeg');

        return (string) $image;
    }
}
