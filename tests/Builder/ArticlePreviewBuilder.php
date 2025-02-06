<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Model\Author;
use Faker\Factory;

final class ArticlePreviewBuilder
{
    private string|NotSet $id = NotSet::VALUE;

    private string|NotSet $title = NotSet::VALUE;

    private string|NotSet $description = NotSet::VALUE;

    private Author|NotSet $author = NotSet::VALUE;

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

    public function withAuthor(Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function build(): ArticlePreview
    {
        $faker = Factory::create();

        $id = $this->id !== NotSet::VALUE ? $this->id : $faker->slug();
        $title = $this->title !== NotSet::VALUE ? $this->title : $faker->sentence();
        $description = $this->description !== NotSet::VALUE ? $this->description : $faker->paragraph(3);
        $author = $this->author !== NotSet::VALUE ? $this->author : anAuthor()->build();

        return new ArticlePreview($id, $title, $description, $author);
    }
}
