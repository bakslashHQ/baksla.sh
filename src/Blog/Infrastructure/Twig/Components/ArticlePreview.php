<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Twig\Components;

use App\Blog\Domain\Model\ArticlePreview as ArticlePreviewModel;
use App\Blog\Domain\Model\Author;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class ArticlePreview
{
    public string $id;

    public string $title;

    public string $description;

    public Author $author;

    public function mount(ArticlePreviewModel $article): void
    {
        $this->id = $article->id;
        $this->title = $article->title;
        $this->description = $article->description;
        $this->author = $article->author;
    }
}
