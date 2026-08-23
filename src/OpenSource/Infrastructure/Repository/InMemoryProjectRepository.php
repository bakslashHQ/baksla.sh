<?php

declare(strict_types=1);

namespace App\OpenSource\Infrastructure\Repository;

use App\OpenSource\Domain\Model\Project;
use App\OpenSource\Domain\Model\ProjectId;
use App\OpenSource\Domain\Repository\ProjectRepository;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(constructor: 'createDefault')]
final readonly class InMemoryProjectRepository implements ProjectRepository
{
    /**
     * @var array<value-of<ProjectId>, Project>
     */
    private array $projects;

    /**
     * @param list<Project> $projects
     */
    public function __construct(array $projects = [])
    {
        $indexedProjects = [];

        foreach ($projects as $project) {
            $indexedProjects[$project->id->value] = $project;
        }

        $this->projects = $indexedProjects;
    }

    public static function createDefault(): self
    {
        return new self([
            new Project(ProjectId::Symfony, 'Symfony', [
                'symfony/symfony',
                'symfony/symfony-docs',
                'symfony/demo',
                'symfony/polyfill',
                'symfony/recipes',
                'symfony/recipes-contrib',
                'symfony/maker-bundle',
                'symfony/monolog-bundle',
                'symfony/mercure',
                'symfony/mercure-bundle',
                'symfony/panther',
                'symfony/ai',
            ]),
            new Project(ProjectId::SymfonyUx, 'Symfony UX', [
                'symfony/ux',
                'symfony/ux.symfony.com',
            ]),
            new Project(ProjectId::SymfonyReprise, 'Reprise', [
                'symfony/reprise',
            ]),
            new Project(ProjectId::WebpackEncore, 'Webpack Encore', [
                'symfony/webpack-encore',
                'symfony/webpack-encore-bundle',
            ]),
            new Project(ProjectId::ApiPlatform, 'API Platform', [
                'api-platform/core',
            ]),
            new Project(ProjectId::Sylius, 'Sylius', [
                'Sylius/Sylius',
                'Sylius/Stack',
                'Sylius/SyliusGridBundle',
                'Sylius/SyliusResourceBundle',
            ]),
            new Project(ProjectId::LexikJwt, 'LexikJWTAuthBundle', [
                'lexik/LexikJWTAuthenticationBundle',
            ]),
            new Project(ProjectId::OAuth2ServerBundle, 'OAuth2 Server Bundle', [
                'thephpleague/oauth2-server-bundle',
            ]),
            new Project(ProjectId::Tactician, 'Tactician', [
                'thephpleague/tactician',
                'thephpleague/tactician-bundle',
                'thephpleague/tactian-logger',
            ]),
            new Project(ProjectId::BiomeJsBundle, 'BiomeJsBundle', [
                'Kocal/BiomeJsBundle',
            ]),
            new Project(ProjectId::PhpstanSymfonyUx, 'PHPStan Symfony UX', [
                'Kocal/phpstan-symfony-ux',
            ]),
        ]);
    }

    public function findAll(): array
    {
        return $this->projects;
    }
}
