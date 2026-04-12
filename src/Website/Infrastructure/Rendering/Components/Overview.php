<?php

declare(strict_types=1);

namespace App\Website\Infrastructure\Rendering\Components;

use App\Team\Domain\Repository\MemberRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final readonly class Overview
{
    public function __construct(
        private MemberRepository $memberRepository,
    ) {
    }

    public function getCollaboratorsCount(): int
    {
        return \count($this->memberRepository->findAll());
    }

    public function getCoreTeamCount(): int
    {
        $count = 0;

        foreach ($this->memberRepository->findAll() as $member) {
            foreach ($member->badges as $badge) {
                if ($badge->uxIconLabel === 'Symfony' && \in_array($badge->text, ['Core Team', 'UX Core Team'], true)) {
                    ++$count;

                    break;
                }
            }
        }

        return $count;
    }

    public function getCertifiedCount(): int
    {
        $count = 0;

        foreach ($this->memberRepository->findAll() as $member) {
            foreach ($member->badges as $badge) {
                if ($badge->uxIconLabel === 'Symfony' && $badge->text === 'Certified') {
                    ++$count;

                    break;
                }
            }
        }

        return $count;
    }
}
