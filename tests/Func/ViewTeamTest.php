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

        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h2', 'home.tabs.team.title');
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
        // New structure uses buttons with data-open-member-modal
        $reviewers = $crawler->filter('[data-open-member-modal]')->each(static fn (Crawler $button): string => trim($button->text()));

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
}
