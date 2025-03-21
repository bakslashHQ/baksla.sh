<?php

declare(strict_types=1);

namespace App\Team\Domain\Exception;

use App\Team\Domain\Model\MemberId;

final class MissingMemberException extends \LogicException
{
    public function __construct(MemberId $id)
    {
        parent::__construct(sprintf('"%s" member does not exist.', $id->value));
    }
}
