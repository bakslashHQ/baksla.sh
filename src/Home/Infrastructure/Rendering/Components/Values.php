<?php

declare(strict_types=1);

namespace App\Home\Infrastructure\Rendering\Components;

use App\Blog\Domain\Model\Article;
use App\Blog\Domain\Repository\ArticleRepository;
use App\OpenSource\Domain\Model\ProjectId;
use App\OpenSource\Domain\Repository\ProjectRepository;
use App\Team\Domain\Model\Member;
use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Repository\MemberRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Home:Values', template: 'components/Home/Values.html.twig')]
final class Values
{
    private const string SECURITY_ARTICLE_ID = 'fixing-symfony-ux-security-vulnerabilities';

    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly MemberRepository $memberRepository,
        private readonly ArticleRepository $articleRepository,
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

    public function getSecurityArticle(): Article
    {
        return $this->articleRepository->get(self::SECURITY_ARTICLE_ID);
    }
}
