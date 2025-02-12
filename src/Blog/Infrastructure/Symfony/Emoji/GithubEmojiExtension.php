<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Symfony\Emoji;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

final readonly class GithubEmojiExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addInlineParser(new GitHubEmojiInlineParser());
    }
}
