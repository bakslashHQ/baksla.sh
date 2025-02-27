<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use App\Blog\Domain\Model\Author;
use Faker\Factory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class AuthorBuilder
{
    private string|NotSet $name = NotSet::VALUE;

    private string|NotSet $picture = NotSet::VALUE;

    private string|NotSet|null $bsky = NotSet::VALUE;

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function withBsky(?string $bksy): self
    {
        $this->bsky = $bksy;

        return $this;
    }

    public function build(): Author
    {
        $faker = Factory::create();

        $name = $this->name !== NotSet::VALUE ? $this->name : $faker->name();

        /** @var string|null $bsky */
        $bsky = $this->bsky !== NotSet::VALUE ? $this->bsky : $faker->randomElement([$faker->userName(), null]);

        $picture = $this->picture;
        if ($picture === NotSet::VALUE) {
            $pictures = (new Finder())
                ->files()
                ->in(sprintf('%s/assets/images/team/members', dirname(__DIR__, 2)))
                ->ignoreVCSIgnored(true);

            /** @var string $picture */
            $picture = $faker->randomElement(array_map(static fn (SplFileInfo $f): string => $f->getFilename(), iterator_to_array($pictures)));
        }

        return new Author($name, $picture, $bsky);
    }
}
