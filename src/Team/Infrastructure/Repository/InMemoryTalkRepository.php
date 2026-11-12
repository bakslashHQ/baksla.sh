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
            new Talk('API Platform, JsonStreamer and ESA', 'API Platform Conference', 2025, '2kHygcXcdQs', [MemberId::MathiasArlaud]),
            new Talk('API Platform & Sylius: a love story', 'API Platform Conference', 2025, 'hECkF7-s3nw', [MemberId::FlorianMerle]),
            new Talk('Symfony UX LiveComponents : construire un dashboard interactif sans une ligne de JS', 'Forum PHP', 2025, 'SZ4lV-K_a8M', [MemberId::FelixEymonot]),
            new Talk('Sous le capot de Composer', 'Forum PHP', 2025, 'OFpvsx8hHEU', [MemberId::FelixEymonot]),
            new Talk("L'orienté objet au cœur du templating Symfony : TwigComponent et LiveComponent", 'Forum PHP', 2024, 'H2r3957IQr0', [MemberId::FelixEymonot]),
            new Talk('Utilisez la bibliothèque standard PHP (SPL) au quotidien', 'Forum PHP', 2023, 'oh-pbz7BC7s', [MemberId::FlorianMerle]),
            new Talk('Json-Ld, Sylius et SEO', 'Meetup Emagma', 2023, 'SE6wM62Apwo', [MemberId::MathiasArlaud]),
            new Talk('Domain-driven design with API Platform 3', 'API Platform Conference', 2022, 'SSQal3Msi9g', [MemberId::MathiasArlaud, MemberId::RobinChalas]),
            new Talk('Du Domain-Driven Design avec API Platform', 'Forum PHP', 2021, 'coEdCX6sm_Q', [MemberId::MathiasArlaud, MemberId::RobinChalas]),
            new Talk('Suitable serialization with API Platform and Symfony', 'API Platform Conference', 2021, 'mYwL8snWvTs', [MemberId::MathiasArlaud]),
            new Talk('Symfony Messenger : enfin stable', 'API Platform Meetup', 2020, 'NiKzQSLJ5Cs', [MemberId::RobinChalas]),
            new Talk('Guard dans la vraie vie', 'SymfonyLive Paris', 2016, 'GVyO78ZiPOM', [MemberId::JeremyRomey]),
        ]);
    }

    public function findAll(): array
    {
        return $this->talks;
    }
}
