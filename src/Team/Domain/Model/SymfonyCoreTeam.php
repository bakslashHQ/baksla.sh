<?php

declare(strict_types=1);

namespace App\Team\Domain\Model;

/**
 * The Symfony Core Team a member belongs to, as listed on https://symfony.com/doc/current/contributing/core_team.html#active-core-members.
 */
enum SymfonyCoreTeam: string
{
    case Framework = 'Core Team';
    case Ux = 'UX Core Team';
}
