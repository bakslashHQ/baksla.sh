<?php

declare(strict_types=1);

namespace App\OpenSource\Domain\Model;

final readonly class Project
{
    /**
     * @param non-empty-string                 $label
     * @param non-empty-list<non-empty-string> $repositories GitHub "owner/name" slugs
     */
    public function __construct(
        public ProjectId $id,
        public string $label,
        public array $repositories,
    ) {
    }

    /**
     * GitHub search listing the pull requests authored by any of the given users across the project's repositories.
     *
     * @param list<non-empty-string> $authors
     * @param list<non-empty-string> $filters extra GitHub search qualifiers, such as "is:merged" or "label:Bug"
     */
    public function getPullRequestsUrl(array $authors, array $filters = []): string
    {
        $qualifiers = [
            'is:pr',
            ...$filters,
            ...array_map(static fn (string $repository): string => \sprintf('repo:%s', $repository), $this->repositories),
            ...array_map(static fn (string $author): string => \sprintf('author:%s', $author), $authors),
        ];

        return \sprintf('https://github.com/search?%s', http_build_query([
            'type' => 'pullrequests',
            'q' => implode(' ', $qualifiers),
        ], encoding_type: \PHP_QUERY_RFC3986));
    }
}
