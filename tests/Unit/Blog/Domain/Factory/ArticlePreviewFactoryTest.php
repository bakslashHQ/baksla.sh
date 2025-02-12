<?php

declare(strict_types=1);

namespace App\Tests\Unit\Blog\Domain\Factory;

use App\Blog\Domain\Factory\ArticlePreviewFactory;
use App\Blog\Domain\Model\ArticlePreview;
use App\Blog\Domain\Model\Author;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class ArticlePreviewFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $yaml = Yaml::dump([
            'author' => 'author',
            'authorPicture' => 'authorPicture',
            'authorBsky' => 'authorBsky',
            'title' => 'title',
            'description' => 'description',
        ]);
        $content = sprintf("---\n%s\n---", $yaml);

        $preview = (new ArticlePreviewFactory())->create('1', $content);

        $this->assertInstanceOf(ArticlePreview::class, $preview);
        $this->assertSame('title', $preview->title);
        $this->assertSame('description', $preview->description);

        $author = $preview->author;
        $this->assertInstanceOf(Author::class, $author);
        $this->assertSame('author', $author->name);
        $this->assertSame('authorPicture', $author->picture);
        $this->assertSame('authorBsky', $author->bsky);
    }

    public function testCreateThrowsWhenNoMetadata(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot find metadata of file "1.md.twig".');

        (new ArticlePreviewFactory())->create('1', 'anything');
    }

    public function testCreateThrowsWhenInvalidYaml(): void
    {
        $yaml = 'foo: bar: baz';
        $content = sprintf("---\n%s\n---", $yaml);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Cannot parse metadata of file "1\.md\.twig": ".+"\.$/');

        (new ArticlePreviewFactory())->create('1', $content);
    }

    /**
     * @param array<string, mixed> $metadata
     */
    #[DataProvider('createThrowsWhenMissingMandatoryMetadataDataProvider')]
    public function testCreateThrowsWhenMissingMandatoryMetadata(string $expectedMissing, array $metadata): void
    {
        $yaml = Yaml::dump($metadata);
        $content = sprintf("---\n%s\n---", $yaml);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Missing "%s" metadata in file "1.md.twig".', $expectedMissing));

        (new ArticlePreviewFactory())->create('1', $content);
    }

    /**
     * @return iterable<array{0: string, 1: array<string, mixed>}>
     */
    public static function createThrowsWhenMissingMandatoryMetadataDataProvider(): iterable
    {
        yield [
            'author', [
                'authorPicture' => 'authorPicture',
                'title' => 'title',
                'description' => 'description',
            ]];
        yield [
            'authorPicture', [
                'author' => 'author',
                'title' => 'title',
                'description' => 'description',
            ]];
        yield [
            'title', [
                'author' => 'author',
                'authorPicture' => 'authorPicture',
                'description' => 'description',
            ]];
        yield [
            'description', [
                'author' => 'author',
                'authorPicture' => 'authorPicture',
                'title' => 'title',
            ]];
    }
}
