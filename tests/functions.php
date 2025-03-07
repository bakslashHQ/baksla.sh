<?php

declare(strict_types=1);

use App\Tests\Builder\ArticleBuilder;
use App\Tests\Builder\ArticlePreviewBuilder;
use App\Tests\Builder\MemberBuilder;

function anArticlePreview(): ArticlePreviewBuilder
{
    return new ArticlePreviewBuilder();
}

function anArticle(): ArticleBuilder
{
    return new ArticleBuilder();
}

function aMember(): MemberBuilder
{
    return new MemberBuilder();
}
