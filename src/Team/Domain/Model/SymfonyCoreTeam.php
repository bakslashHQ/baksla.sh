<?php

declare(strict_types=1);

namespace App\Team\Domain\Model;

/**
 * The Symfony Core Team a member belongs to, as listed on https://symfony.com/doc/current/contributing/code/core_team.html.
 */
enum SymfonyCoreTeam: string
{
    case Framework = 'Core Team';
    case Ux = 'UX Core Team';
}
