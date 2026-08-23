<?php

declare(strict_types=1);

use App\Tests\Builder\ArticleBuilder;
use App\Tests\Builder\MemberBuilder;
use App\Tests\Builder\ProjectBuilder;

function anArticle(): ArticleBuilder
{
    return new ArticleBuilder();
}

function aMember(): MemberBuilder
{
    return new MemberBuilder();
}

function aProject(): ProjectBuilder
{
    return new ProjectBuilder();
}
