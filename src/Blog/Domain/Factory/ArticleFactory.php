<?php

declare(strict_types=1);

namespace App\Blog\Domain\Factory;

use App\Blog\Domain\Factory\ArticleFactory\HtmlGenerator;
use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticlePreviewRepository;

final readonly class ArticleFactory
{
    public function __construct(
        private ArticlePreviewRepository $articlePreviewRepository,
        private HtmlGenerator $htmlGenerator,
    ) {
    }

    public function create(string $id): Article
    {
        $preview = $this->articlePreviewRepository->get($id);

        return new Article(
            id: $id,
            title: $preview->title,
            description: $preview->description,
            html: $this->htmlGenerator->generate($id),
            author: $preview->author,
        );
    }
}
