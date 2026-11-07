<?php

declare(strict_types=1);

namespace App\Team\Infrastructure\Controller;

use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Repository\MemberRepository;
use Bakslash\StaticSiteBundle\Attribute\Prerender;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class ViewTeam
{
    public function __construct(
        private MemberRepository $memberRepository,
    ) {
    }

    /**
     * @return array{members: array<value-of<MemberId>, Member>}
     */
    #[Route(path: '/team', name: 'app_team', methods: ['GET'])]
    #[Prerender]
    #[Template('pages/team/index.html.twig')]
    public function __invoke(): array
    {
        return [
            'members' => $this->memberRepository->findAll(),
        ];
    }
}
