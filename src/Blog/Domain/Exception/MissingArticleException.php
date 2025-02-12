<?php

declare(strict_types=1);

namespace App\Blog\Domain\Exception;

final class MissingArticleException extends \LogicException
{
    public function __construct(string $id)
    {
        parent::__construct(sprintf('"%s" article does not exist.', $id));
    }
}
