<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\OpenGraph;

use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\LocaleSwitcher;

#[AsCommand(name: 'blog:generate-og-images', description: 'Generates OpenGraph images for blog articles')]
final class GenerateOpenGraphImagesCommand extends Command
{
    /**
     * @param list<string> $enabledLocales
     */
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly ImageGenerator $imageGenerator,
        private readonly Filesystem $filesystem,
        private readonly LocaleSwitcher $localeSwitcher,
        #[Autowire(param: 'app.public_dir')]
        private readonly string $publicDir,
        #[Autowire(param: 'kernel.enabled_locales')]
        private readonly array $enabledLocales,
        #[Autowire(param: 'kernel.default_locale')]
        private readonly string $defaultLocale,
    ) {
        parent::__construct();
    }

    public function __invoke(SymfonyStyle $io): int
    {
        foreach ($this->enabledLocales as $locale) {
            $articles = $this->localeSwitcher->runWithLocale($locale, fn (): array => $this->articleRepository->findAll());

            foreach ($articles as $article) {
                // The default locale keeps the original "{id}.jpg" path for backward compatibility
                $filename = $locale === $this->defaultLocale
                    ? sprintf('%s.jpg', $article->id)
                    : sprintf('%s.%s.jpg', $article->id, $locale);

                $path = sprintf('%s/open-graph/article/%s', $this->publicDir, $filename);
                $this->filesystem->dumpFile($path, $this->imageGenerator->generate($article, $locale));
                $io->info(sprintf('Generated OG image for "%s" (%s)', $article->id, $locale));
            }
        }

        return Command::SUCCESS;
    }
}
