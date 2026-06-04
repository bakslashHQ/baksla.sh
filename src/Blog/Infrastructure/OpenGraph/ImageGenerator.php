<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\OpenGraph;

use App\Blog\Domain\Model\Article;
use Playwright\Playwright;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class ImageGenerator
{
    private const int WIDTH = 1200;

    private const int HEIGHT = 630;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        #[Autowire(param: 'app.og_preview_base_url')]
        private string $baseUrl,
    ) {
    }

    public function generate(Article $article): string
    {
        $path = $this->urlGenerator->generate('app_blog_og_preview', [
            'id' => $article->id,
        ]);
        $url = rtrim($this->baseUrl, '/') . $path;

        $context = Playwright::chromium([
            'headless' => true,
            'context' => [
                'viewport' => [
                    'width' => self::WIDTH,
                    'height' => self::HEIGHT,
                ],
                'deviceScaleFactor' => 1,
                'reducedMotion' => 'reduce',
            ],
        ]);

        try {
            $page = $context->newPage();
            $page->goto($url, [
                'waitUntil' => 'networkidle',
            ]);
            $page->waitForSelector('body[data-og-ready="true"]', [
                'timeout' => 15000,
            ]);
            $screenshotFile = $page->screenshot(options: [
                'type' => 'jpeg',
                'quality' => 95,
                'clip' => [
                    'x' => 0,
                    'y' => 0,
                    'width' => self::WIDTH,
                    'height' => self::HEIGHT,
                ],
            ]);

            $contents = file_get_contents($screenshotFile);
            if ($contents === false) {
                throw new \RuntimeException(sprintf('Failed to read generated OG image from "%s".', $screenshotFile));
            }

            return $contents;
        } finally {
            $context->close();
            if (isset($screenshotFile) && file_exists($screenshotFile)) {
                unlink($screenshotFile);
            }
        }
    }
}
