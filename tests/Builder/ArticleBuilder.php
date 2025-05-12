<?php

declare(strict_types=1);

namespace App\Tests\Builder;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Infrastructure\Rendering\CodeBlock\TempestCodeBlockRenderer;
use App\Blog\Infrastructure\Rendering\LeagueMarkdownConverter;
use App\Team\Domain\Model\Member;
use Faker\Factory;
use Symfony\Component\Finder\Finder;

final class ArticleBuilder
{
    private string|NotSet $id = NotSet::VALUE;

    private string|NotSet $title = NotSet::VALUE;

    private string|NotSet $description = NotSet::VALUE;

    private string|NotSet $html = NotSet::VALUE;

    private Member|NotSet $author = NotSet::VALUE;

    public function withPreview(ArticlePreview $preview): self
    {
        $this->id = $preview->id;
        $this->title = $preview->title;
        $this->description = $preview->description;
        $this->author = $preview->author;

        return $this;
    }

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

    public function withHtml(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function withAuthor(Member $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function build(): Article
    {
        $faker = Factory::create();

        $id = $this->id !== NotSet::VALUE ? $this->id : $faker->slug();
        $title = $this->title !== NotSet::VALUE ? $this->title : $faker->sentence();
        $description = $this->description !== NotSet::VALUE ? $this->description : $faker->paragraph(3);
        $author = $this->author !== NotSet::VALUE ? $this->author : aMember()->build();

        $html = $this->html;
        if ($html === NotSet::VALUE) {
            $markdowns = iterator_to_array((new Finder())
                ->files()
                ->in(sprintf('%s/templates/articles/', dirname(__DIR__, 2)))
                ->name('*.md.twig')
                ->ignoreVCSIgnored(true));

            $markdown = $markdowns[array_rand($markdowns)];

            $converter = new LeagueMarkdownConverter(new TempestCodeBlockRenderer());
            $html = $converter->convert($markdown->getContents());
        }

        return new Article($id, $title, $description, $html, $author);
    }
}
