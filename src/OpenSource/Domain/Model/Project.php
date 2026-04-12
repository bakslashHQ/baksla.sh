<?php

declare(strict_types=1);

namespace App\OpenSource\Domain\Model;

enum Project: string
{
    case Symfony = 'symfony';
    case ApiPlatform = 'api-platform';
    case Sylius = 'sylius';
    case LexikJwt = 'lexik-jwt';
    case Oauth2ServerBundle = 'oauth2-server-bundle';
    case BiomeJsBundle = 'biome-js-bundle';
    case OxcBundle = 'oxc-bundle';

    public function label(): string
    {
        return match ($this) {
            self::Symfony => 'Symfony',
            self::ApiPlatform => 'API Platform',
            self::Sylius => 'Sylius',
            self::LexikJwt => 'LexikJWTAuthenticationBundle',
            self::Oauth2ServerBundle => 'OAuth2 Server Bundle',
            self::BiomeJsBundle => 'BiomeJsBundle',
            self::OxcBundle => 'OxcBundle',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Symfony => 'logos:symfony',
            self::ApiPlatform => 'logos:api-platform',
            self::Sylius => 'logos:sylius',
            self::LexikJwt => 'logos:symfony',
            self::Oauth2ServerBundle => 'logos:symfony',
            self::BiomeJsBundle => 'logos:symfony',
            self::OxcBundle => 'logos:symfony',
        };
    }

    public function url(): string
    {
        return match ($this) {
            self::Symfony => 'https://github.com/symfony/symfony',
            self::ApiPlatform => 'https://github.com/api-platform/core',
            self::Sylius => 'https://github.com/Sylius/Sylius',
            self::LexikJwt => 'https://github.com/lexik/LexikJWTAuthenticationBundle',
            self::Oauth2ServerBundle => 'https://github.com/thephpleague/oauth2-server-bundle',
            self::BiomeJsBundle => 'https://github.com/Kocal/BiomeJsBundle',
            self::OxcBundle => 'https://github.com/Kocal/OxcBundle',
        };
    }

    public function isBundle(): bool
    {
        return match ($this) {
            self::LexikJwt, self::Oauth2ServerBundle, self::BiomeJsBundle, self::OxcBundle => true,
            default => false,
        };
    }
}
