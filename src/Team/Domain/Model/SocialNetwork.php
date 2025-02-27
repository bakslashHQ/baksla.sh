<?php

declare(strict_types=1);

namespace App\Team\Domain\Model;

final readonly class SocialNetwork
{
    private function __construct(
        public string $label,
        public string $url,
        public string $uxIcon,
    ) {
    }

    public static function github(string $username): self
    {
        return new self($username, sprintf('https://github.com/%s', $username), 'logos:github');
    }

    public static function bluesky(string $username): self
    {
        return new self($username, sprintf('https://bluesky.dev/%s', $username), 'logos:bluesky');
    }

    public static function twitter(string $username): self
    {
        return new self($username, sprintf('https://twitter.com/%s', $username), 'logos:x');
    }

    public static function symfony(string $username): self
    {
        return new self($username, sprintf('https://connect.symfony.com/profile/%s', $username), 'logos:symfony');
    }

    public static function linkedin(string $username): self
    {
        return new self($username, sprintf('https://www.linkedin.com/in/%s', $username), 'logos:linkedin-icon');
    }
}
