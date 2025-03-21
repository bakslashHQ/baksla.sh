<?php

declare(strict_types=1);

namespace App\Tests\Func;

use App\Team\Domain\Repository\MemberRepository;
use Symfony\Component\DomCrawler\Crawler;

final class ViewTeamTest extends FunctionalTestCase
{
    public function testRenderProperHtml(): void
    {
        $this->get('/team');

        $this->assertSelectorTextContains('h1', 'team.title');
        $this->assertSelectorExists('[data-test-review]');
    }

    public function testEveryTeamMemberHasHisModal(): void
    {
        $this->get('/team');

        foreach ($this->getService(MemberRepository::class)->findAll() as $member) {
            $this->assertSelectorExists(sprintf('[data-member-modal-member-value="%s"]', $member->id->value));
        }
    }

    public function testEveryTeamMemberIsListedAsReviewer(): void
    {
        $crawler = $this->get('/team');
        $reviewers = $crawler->filter('[data-test-reviewers] [data-test-member-full-name]')->each(static fn (Crawler $a): string => $a->text());

        foreach ($this->getService(MemberRepository::class)->findAll() as $member) {
            $this->assertContains($member->getFullname(), $reviewers);
        }
    }

    public function testEveryTeamMemberComments(): void
    {
        $crawler = $this->get('/team');
        $commenters = $crawler->filter('[data-test-review-comment] [data-test-review-comment-reviewer]')->each(static fn (Crawler $a): string => $a->text());

        foreach ($this->getService(MemberRepository::class)->findAll() as $member) {
            $this->assertContains($member->getFullname(), $commenters);
        }
    }

    public function testReturnCachedVersionWhenPossible(): void
    {
        $memberRepository = $this->getService(MemberRepository::class);

        $this->get('/team');
        $this->assertResponseStatusCodeSame(200);

        $this->get('/team', server: [
            'HTTP_IF_NONE_MATCH' => sprintf('"%s"', $memberRepository->getHash()),
        ]);
        $this->assertResponseStatusCodeSame(304);
    }
}
