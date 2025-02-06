<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony\Translation;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[When(env: 'test')]
#[AsDecorator('translator')]
final readonly class NoopTranslator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface
{
    public function __construct(
        #[AutowireDecorated]
        private TranslatorInterface&TranslatorBagInterface&LocaleAwareInterface $decorated,
    ) {
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $id;
    }

    public function getLocale(): string
    {
        return $this->decorated->getLocale();
    }

    public function setLocale(string $locale): void
    {
        $this->decorated->setLocale($locale);
    }

    public function getCatalogue(?string $locale = null): MessageCatalogueInterface
    {
        return $this->decorated->getCatalogue($locale);
    }

    public function getCatalogues(): array
    {
        return $this->decorated->getCatalogues();
    }
}
