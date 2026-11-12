<?php

declare(strict_types=1);

namespace App\Home\Infrastructure\Rendering\Components;

use App\OpenSource\Domain\Model\ProjectId;
use App\OpenSource\Domain\Repository\ProjectRepository;
use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Model\Talk;
use App\Team\Domain\Repository\MemberRepository;
use App\Team\Domain\Repository\TalkRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Home:Values', template: 'components/Home/Values.html.twig')]
final class Values
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly MemberRepository $memberRepository,
        private readonly TalkRepository $talkRepository,
    ) {
    }

    public function getSymfonyPullRequestsUrl(): string
    {
        $symfony = $this->projectRepository->findAll()[ProjectId::Symfony->value] ?? throw new \LogicException('The Symfony project is missing.');

        return $symfony->getPullRequestsUrl($this->memberRepository->findGithubUsernames());
    }

    /**
     * @return array<value-of<MemberId>, Member>
     */
    public function getMembers(): array
    {
        return $this->memberRepository->findAll();
    }

    /**
     * @return list<Talk>
     */
    public function getTalks(): array
    {
        return $this->talkRepository->findAll();
    }
}
