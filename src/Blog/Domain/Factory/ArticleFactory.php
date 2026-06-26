<?php

declare(strict_types=1);

namespace App\Blog\Domain\Factory;

use App\Blog\Domain\Factory\ArticleFactory\HtmlProvider;
use App\Blog\Domain\Factory\ArticleFactory\MetadataProvider;
use App\Blog\Domain\Model\Article;
use App\Team\Domain\Repository\MemberRepository;

final readonly class ArticleFactory
{
    public function __construct(
        private MemberRepository $memberRepository,
        private MetadataProvider $metadataProvider,
        private HtmlProvider $htmlProvider,
    ) {
    }

    public function create(string $id): Article
    {
        $metadata = $this->metadataProvider->provide($id);

        $reflector = new \ReflectionClass(Article::class);
        $htmlProperty = $reflector->getProperty('html');

        $article = $reflector->newLazyGhost(function (Article $article) use ($id, $htmlProperty): void {
            $htmlProperty->setRawValueWithoutLazyInitialization($article, $this->htmlProvider->provide($id));
        });

        $properties = [
            'id' => $id,
            'slug' => $metadata->slug,
            'title' => $metadata->title,
            'description' => $metadata->description,
            'author' => $this->memberRepository->get($metadata->authorId),
            'publishedAt' => $metadata->publishedAt,
        ];

        foreach ($properties as $property => $value) {
            $reflector->getProperty($property)->setRawValueWithoutLazyInitialization($article, $value);
        }

        return $article;
    }
}
