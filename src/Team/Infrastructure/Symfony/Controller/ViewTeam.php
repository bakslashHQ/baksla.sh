<?php

declare(strict_types=1);

namespace App\Team\Infrastructure\Symfony\Controller;

use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Repository\MemberRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[AsController]
final readonly class ViewTeam
{
    public function __construct(
        private Environment $twig,
        private MemberRepository $memberRepository,
    ) {
    }

    #[Route(name: 'app_team', path: '/team', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new Response($this->twig->render('pages/team/index.html.twig', [
            'members' => $this->memberRepository->findAll(),
        ]));
    }
}
