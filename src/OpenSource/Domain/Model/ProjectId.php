<?php

declare(strict_types=1);

namespace App\OpenSource\Domain\Model;

enum ProjectId: string
{
    case ApiPlatform = 'api-platform';
    case BiomeJsBundle = 'biome-js-bundle';
    case LexikJwt = 'lexik-jwt';
    case OAuth2ServerBundle = 'oauth2-server-bundle';
    case PhpstanSymfonyUx = 'phpstan-symfony-ux';
    case Symfony = 'symfony';
    case SymfonyReprise = 'symfony-reprise';
    case SymfonyUx = 'symfony-ux';
    case Sylius = 'sylius';
    case Tactician = 'tactician';
    case WebpackEncore = 'webpack-encore';
}
