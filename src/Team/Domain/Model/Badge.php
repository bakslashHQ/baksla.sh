<?php

declare(strict_types=1);

namespace App\Team\Domain\Model;

final readonly class Badge
{
    /**
     * @param non-empty-string      $text
     * @param non-empty-string|null $uxIcon
     * @param non-empty-string|null $uxIconLabel
     */
    private function __construct(
        public string $text,
        public ?string $uxIcon = null,
        public ?string $uxIconLabel = null,
    ) {
    }

    /**
     * @param non-empty-string $text
     */
    public static function raw(string $text): self
    {
        return new self($text);
    }

    /**
     * @param non-empty-string $position
     */
    public static function bakslashPosition(string $position): self
    {
        return new self($position, 'logos:bakslash', 'baksla.sh');
    }

    /**
     * @param non-empty-string $award
     */
    public static function symfonyAward(string $award): self
    {
        return new self($award, 'logos:symfony', 'Symfony');
    }

    /**
     * @param non-empty-string $award
     */
    public static function phpAward(string $award): self
    {
        return new self($award, 'logos:php', 'PHP');
    }
}
