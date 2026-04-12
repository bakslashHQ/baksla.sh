<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use App\Blog\Domain\Model\ArticlePreview;
use App\Team\Domain\Model\Member;
use Faker\Factory;

final class ArticlePreviewBuilder
{
    private string|NotSet $id = NotSet::VALUE;

    private string|NotSet $title = NotSet::VALUE;

    private string|NotSet $description = NotSet::VALUE;

    private Member|NotSet $author = NotSet::VALUE;

    private \DateTimeImmutable|NotSet $publishedAt = NotSet::VALUE;

    public function withId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function withDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function withAuthor(Member $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function withPublishedAt(\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function build(): ArticlePreview
    {
        $faker = Factory::create();

        $id = $this->id !== NotSet::VALUE ? $this->id : $faker->slug();
        $title = $this->title !== NotSet::VALUE ? $this->title : $faker->sentence();
        $description = $this->description !== NotSet::VALUE ? $this->description : $faker->paragraph(3);
        $author = $this->author !== NotSet::VALUE ? $this->author : aMember()->build();
        $publishedAt = $this->publishedAt !== NotSet::VALUE ? $this->publishedAt : \DateTimeImmutable::createFromMutable($faker->dateTime());

        return new ArticlePreview($id, $title, $description, $author, $publishedAt);
    }
}
