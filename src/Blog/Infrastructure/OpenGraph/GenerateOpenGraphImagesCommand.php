<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\OpenGraph;

use App\Blog\Domain\OpenGraph\ImageGenerator;
use App\Blog\Domain\Repository\ArticleRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(name: 'blog:generate-og-images', description: 'Generates OpenGraph images for blog articles')]
final class GenerateOpenGraphImagesCommand extends Command
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly ImageGenerator $imageGenerator,
        private readonly Filesystem $filesystem,
        #[Autowire(param: 'app.public_dir')]
        private readonly string $publicDir,
    ) {
        parent::__construct();
    }

    public function __invoke(SymfonyStyle $io): int
    {
        foreach ($this->articleRepository->findAll() as $article) {
            $path = sprintf('%s/open-graph/article/%s.jpg', $this->publicDir, $article->id);
            $this->filesystem->dumpFile($path, $this->imageGenerator->generate($article));
            $io->info(sprintf('Generated OG image for "%s"', $article->id));
        }

        return Command::SUCCESS;
    }
}
