<?php

declare(strict_types=1);

namespace App\Team\Domain\Model;

final readonly class SocialNetwork
{
    /**
     * @param non-empty-string $url
     * @param non-empty-string $uxIcon
     * @param non-empty-string $uxIconLabel
     */
    private function __construct(
        public string $url,
        public string $uxIcon,
        public string $uxIconLabel,
    ) {
    }

    public static function github(string $username): self
    {
        return new self(sprintf('https://github.com/%s', $username), 'logos:github', 'Github');
    }

    public static function bluesky(string $username): self
    {
        return new self(sprintf('https://bluesky.dev/%s', $username), 'logos:bluesky', 'Bluesky');
    }

    public static function twitter(string $username): self
    {
        return new self(sprintf('https://twitter.com/%s', $username), 'logos:x', 'X');
    }

    public static function symfony(string $username): self
    {
        return new self(sprintf('https://connect.symfony.com/profile/%s', $username), 'logos:symfony', 'Symfony');
    }

    public static function linkedin(string $username): self
    {
        return new self(sprintf('https://www.linkedin.com/in/%s', $username), 'logos:linkedin-icon', 'Linkedin');
    }
}
