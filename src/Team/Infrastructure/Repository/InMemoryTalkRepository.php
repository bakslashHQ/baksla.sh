<?php

declare(strict_types=1);

namespace App\Team\Infrastructure\Repository;

use App\Team\Domain\Model\MemberId;
use App\Team\Domain\Model\Talk;
use App\Team\Domain\Repository\TalkRepository;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(constructor: 'createDefault')]
final readonly class InMemoryTalkRepository implements TalkRepository
{
    /**
     * @var list<Talk>
     */
    private array $talks;

    /**
     * @param list<Talk> $talks
     */
    public function __construct(array $talks = [])
    {
        usort($talks, static fn (Talk $a, Talk $b): int => $b->year <=> $a->year);

        $this->talks = $talks;
    }

    /**
     * Thumbnails live in assets/images/talks/{youtubeId}.webp: add one when adding a talk.
     */
    public static function createDefault(): self
    {
        return new self([
            new Talk('DDD x API Platform: 4 years later', 'API Platform Conference', 2026, 'BZd6PGYzFXs', [MemberId::MathiasArlaud]),
            new Talk('Leveraging the ExpressionLanguage component', 'Fork it! Tunis', 2026, '0mWkjQQQ7nM', [MemberId::MathiasArlaud]),
            new Talk('Shaping Symfony 8, Shipping Symfony 8: lessons from the inside', 'Fork it! Hanoi', 2026, 'P7H4-VpM4A0', [MemberId::MathiasArlaud, MemberId::RobinChalas]),
            new Talk("Let's Break & Fix Symfony: live contribution session", 'Never Code Alone', 2026, '6HsMnLzDAOw', [MemberId::MathiasArlaud]),
            new Talk("Dépréciations Symfony : de l'angoisse à l'opportunité", 'Forum PHP', 2025, '7qG03GHRSNU', [MemberId::MathiasArlaud]),
            new Talk('Mastering Deprecations: smooth upgrades with Symfony and Sylius', 'SyliusCon', 2025, 'lBZd-H4YFgU', [MemberId::MathiasArlaud]),
            new Talk('API Platform, JsonStreamer and ESA', 'API Platform Conference', 2025, '2kHygcXcdQs', [MemberId::MathiasArlaud]),
            new Talk('API Platform & Sylius: a love story', 'API Platform Conference', 2025, 'hECkF7-s3nw', [MemberId::FlorianMerle]),
            new Talk('Symfony UX LiveComponents : construire un dashboard interactif sans une ligne de JS', 'Forum PHP', 2025, 'SZ4lV-K_a8M', [MemberId::FelixEymonot]),
            new Talk('Sous le capot de Composer', 'Forum PHP', 2025, 'OFpvsx8hHEU', [MemberId::FelixEymonot]),
            new Talk("L'orienté objet au cœur du templating Symfony : TwigComponent et LiveComponent", 'Forum PHP', 2024, 'H2r3957IQr0', [MemberId::FelixEymonot]),
            new Talk('Making API Platform serialization superfast', 'API Platform Conference', 2024, 'ObeHMKMmFyk', [MemberId::MathiasArlaud]),
            new Talk('Sérialisation sous stéroïdes avec le composant JsonEncoder', 'Meetup Symfony', 2024, 'bkcb8p6zsz4', [MemberId::RobinChalas, MemberId::MathiasArlaud]),
            new Talk('Subresources, the easy way with API Platform 3', 'API Platform Conference', 2023, 'bTdQZyU0AwE', [MemberId::MathiasArlaud]),
            new Talk('Utilisez la bibliothèque standard PHP (SPL) au quotidien', 'Forum PHP', 2023, 'oh-pbz7BC7s', [MemberId::FlorianMerle]),
            new Talk('Json-Ld, Sylius et SEO', 'Meetup Emagma', 2023, 'SE6wM62Apwo', [MemberId::MathiasArlaud]),
            new Talk('Domain-driven design with API Platform 3', 'API Platform Conference', 2022, 'SSQal3Msi9g', [MemberId::MathiasArlaud, MemberId::RobinChalas]),
            new Talk('A suitable serialization with API Platform and Symfony', 'Apidays Paris', 2022, '5K6TnnZEDro', [MemberId::MathiasArlaud]),
            new Talk('Sortir du cadre', 'Forum PHP', 2022, 'cJQsahDVW58', [MemberId::RobinChalas]),
            new Talk("De l'humain à l'ordinateur, ou découvrir le sens d'un texte avec ElasticSearch", 'Forum PHP', 2022, 'UhONe6GSfGQ', [MemberId::MathiasArlaud]),
            new Talk('Du Domain-Driven Design avec API Platform', 'Forum PHP', 2021, 'coEdCX6sm_Q', [MemberId::MathiasArlaud, MemberId::RobinChalas]),
            new Talk('Suitable serialization with API Platform and Symfony', 'API Platform Conference', 2021, 'mYwL8snWvTs', [MemberId::MathiasArlaud]),
            new Talk('Secure and practical authentication in API Platform', 'API Platform Conference', 2021, 'iISeDdwN5lY', [MemberId::RobinChalas]),
            new Talk('Symfony Messenger : enfin stable', 'API Platform Meetup', 2020, 'NiKzQSLJ5Cs', [MemberId::RobinChalas]),
            new Talk('Guard dans la vraie vie', 'SymfonyLive Paris', 2016, 'GVyO78ZiPOM', [MemberId::JeremyRomey]),
        ]);
    }

    public function findAll(): array
    {
        return $this->talks;
    }
}
