<?php

declare(strict_types=1);

namespace App\Team\Infrastructure\Twig\Components;

use App\Team\Domain\Model\Member;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final readonly class MemberModal
{
    public Member $member;

    public function mount(Member $member): void
    {
        $this->member = $member;
    }
}
